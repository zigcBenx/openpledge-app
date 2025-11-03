<?php

namespace App\Actions\Github;

use App\Actions\Comment\ConstructComment;
use App\Actions\Email\SendNotifyPledgersMail;
use App\Actions\PendingDonation\CreateNewPendingDonation;
use App\Actions\WalletTransaction\CreateNewWalletTransaction;
use App\Models\Issue;
use App\Models\Label;
use App\Models\PendingDonation;
use App\Models\ProgrammingLanguage;
use App\Models\Repository;
use App\Models\User;
use App\Services\GithubService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WebhookActions
{
    public static function handleWebhookRepository($payload): void
    {
        $repositoryId = $payload['repository']['id'];
        $repository = Repository::where('github_id', $repositoryId)->first();

        if (!$repository) {
            return;
        }

        $repositoryIssues = Issue::where('repository_id', $repository->id)->get();
        foreach ($repositoryIssues as $issue) {
            $issueGithubUrl = $issue->github_url;
            $issueGithubUrl = str_replace($repository->title, $payload['repository']['full_name'], $issueGithubUrl);
            $issue->github_url = $issueGithubUrl;
            $issue->save();
        }

        $repository->title = $payload['repository']['full_name'];
        $repository->github_url = $payload['repository']['html_url'];

        $githubInstallation = $repository->githubInstallation;

        $programmingLanguages = array_keys(GithubService::getRepositoryProgrammingLanguages($repository->title, $githubInstallation->access_token));

        $languageIds = [];
        foreach ($programmingLanguages as $programmingLanguage) {
            $language = ProgrammingLanguage::updateOrCreate(
                ['name' => $programmingLanguage],
                ['name' => $programmingLanguage]
            );
            $languageIds[] = $language->id;
        }

        $repository->programmingLanguages()->sync($languageIds);
        $repository->save();
    }

    /**
     * This webhook is triggered when change occurs on GitHub. It could be
     * just update of it's content, or closing issue by pull request.
     * If closed, then we proceed to pay out donations to contributors.
     *
     * @param $payload
     * @param $action
     * @return void
     */
    public static function handleWebhookIssue($payload, $action): void
    {
        $githubIssue = $payload['issue'];
        $issueGithubId = $githubIssue['id'];
        $issue = Issue::where('github_id', $issueGithubId)->first();
        if (!$issue) {
            // If issue is not in our database, we don't have any donations,
            // and no need to update its existing data in our DB,
            // therefore we don't need to check anything else
            return;
        }

        self::syncIssueWithGithub($issue, $githubIssue, $action);

        $issueDonations = $issue->getUnpaidDonations();
        if ($issueDonations->isEmpty() || $action !== "closed") {
            return;
        }

        $mergedPullRequest = self::findMergedPullRequest($payload, $githubIssue['number']);
        if (!$mergedPullRequest) {
            return;
        }

        $resolverData = self::getResolverData($payload, $mergedPullRequest);
        self::updateIssueResolver($issue, $resolverData);
        $resolverUser = self::getMatchingUser($resolverData);

        if ($resolverUser) {
            self::payoutToResolver($resolverUser, $issueDonations);
        } else {
            // user doesn't exist in OpenPledge database, we add donations to pending and wait
            // until he/she creates account on OP.
            CreateNewPendingDonation::create($issueDonations, $mergedPullRequest['author']['login']);
            self::postCommentOnGithubForResolver($issue, $issueDonations, $resolverData);
        }


        self::notifyPledgers($issue, $issueDonations);
        self::notifyOtherContributorsWorkingOnThisIssue();
    }

    /**
     * We update issue in DB with data from GitHub.
     */
    private static function syncIssueWithGithub($issue, $githubIssue, $action): void
    {
        $issue->state = $action === "closed" ? "closed" : "open";
        $issue->title = $githubIssue['title'];
        $issue->description = $githubIssue['body'];
        $issue->labels()->delete();

        $allowedLabels = Label::$allowedLabels;
        $labels = $githubIssue['labels'];

        foreach ($labels as $label) {
            if (in_array(strtolower($label['name']), $allowedLabels)) {
                $issue->labels()->create([
                    'name' => $label['name']
                ]);
            }
        }

        $issue->save();
    }

    private static function findMergedPullRequest(array $payload, int $issueNumber): ?array
    {
        $repository = $payload['repository'];
        $repoOwner = $repository['owner']['login'];
        $repoName = $repository['name'];

        $pullRequestsResponse = GithubService::getConnectedPullRequests($repoOwner, $repoName, $issueNumber);
        if (!$pullRequestsResponse) {
            return null;
        }

        $mergedPullRequest = collect($pullRequestsResponse['data']['repository']['issue']['timelineItems']['nodes'])
            ->first(function ($item) {
                return (isset($item['subject']) && $item['subject']['state'] === 'MERGED')
                    || (isset($item['source']) && $item['source']['state'] === 'MERGED');
            });

        if (!$mergedPullRequest) {
            return null;
        }

        return $mergedPullRequest['subject'] ?? $mergedPullRequest['source'];
    }

    private static function getResolverData(array $payload, array $mergedPullRequest): array
    {
        $repository = $payload['repository'];
        $pullRequestData = GithubService::getPullRequestData(
            $repository['owner']['login'],
            $repository['name'],
            $mergedPullRequest['number']
        );

        return [
            'github_id'       => $pullRequestData['user']['id'],
            'github_username' => $pullRequestData['user']['login'],
            'merged_at'       => $pullRequestData['merged_at']
        ];
    }

    private static function updateIssueResolver(Issue $issue, array $resolverData): void
    {
        $issue->resolver_github_id = $resolverData['github_id'];
        $issue->resolved_at = Carbon::parse($resolverData['merged_at'])
            ->setTimezone(config('app.timezone'));
        $issue->save();
    }

    private static function getMatchingUser($resolverData): ?User
    {
        return User::where('github_id', $resolverData['github_id'])->first();
    }

    private static function payoutToResolver(User $resolverUser, $donations): void
    {
        CreateNewWalletTransaction::create($resolverUser, $donations);
    }

    private static function notifyPledgers($issue, $donations): void
    {
        $pledgers = $donations->pluck('user')->filter()->values()->toArray();
        SendNotifyPledgersMail::send($pledgers, $issue->id);
    }

    private static function notifyOtherContributorsWorkingOnThisIssue(): void
    {
        // TODO:
        // Query users who have this issue as an active issue but exclude the current resolver
//        $usersWithActiveIssue = User::whereHas('active_issues', function ($query) use ($issueId) {
//            $query->where('issue_id', $issueId);
//        })->where('email', '!=', $resolverMail)->get();
//
//        SendIssueResolverMail::send($resolverMail, $dbUser->name, $issue->id, $usersWithActiveIssue); // Send emails to all resolvers during beta, regardless of Stripe connection
    }

    private static function postCommentOnGithubForResolver($issue, $issueDonations, $resolverData): void
    {
        $comment = ConstructComment::constructCreateAccountComment(
            $issue->id,
            $issueDonations->sum('net_amount'),
            $resolverData['github_username']
        );

        GithubService::commentOnIssue($issue, $comment);
    }
}
