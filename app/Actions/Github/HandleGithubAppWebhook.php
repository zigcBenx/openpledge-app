<?php

namespace App\Actions\Github;

use App\Actions\Email\SendIssueResolverMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Issue;
use App\Models\User;
use App\Models\Donation;
use App\Models\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\Transfer;

class HandleGithubAppWebhook
{
    public static function run(Request $request)
    {
        $payload = $request->all();

        DB::beginTransaction();
        try {
            $action = $payload['action'] ?? null;
            $pullRequest = $payload['pull_request'] ?? null;

            if ($pullRequest && isset($pullRequest['merged']) && $pullRequest['merged']) {
                self::handlePullRequest($pullRequest, $action);
            } elseif (isset($payload['issue'])) {
                self::handleIssue($payload['issue'], $action);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Transaction failed'], 500);
        }
        return response()->json(['status' => 'success'], 200);
    }

    private static function handlePullRequest(array $pullRequest, ?string $action)
    {
        $issueHeadData = $pullRequest['head'];
        $repositoryData = $issueHeadData['repo'];

        $eventsResponse = Http::withToken(self::getAccessToken($repositoryData['html_url']))->get($repositoryData['events_url']);
        $events = $eventsResponse->json();

        $closedIssueUrl = null;

        foreach ($events as $event) {
            if ($event['type'] === 'IssuesEvent' && $event['payload']['action'] === 'closed') {
                $closedIssueUrl = $event['payload']['issue']['html_url'];
                break;
            }
        }

        $issue = Issue::where('github_url', $closedIssueUrl)->firstOrFail();
        $user = $pullRequest['user'];

        $issue->state = $action === "reopened" ? "open" : $action;
        $issue->resolver_github_id = $user['id'];
        $issue->resolved_at = Carbon::parse($pullRequest['merged_at']);
        $issue->save();

        $dbUser = User::where('github_id', $user['id'])->first();

        if ($dbUser) {
            self::processDonations($issue, $dbUser);
        } else {
            logger('[WARNING] No user with the following GitHub id is connected to our app', ['id' => $user['id']]);
            // TODO: Send email to notify the user to register to OpenPledge & connect with Stripe to claim the reward
        }
    }

    public static function getAccessToken($repoUrl)
    {
        $repository = Repository::with('githubInstallation')
        ->where('github_url', $repoUrl)
        ->first();

        if (!$repository) {
            logger('[ERROR] Repository or GitHub installation not found', ['repo_url' => $repoUrl]);
            throw new \Exception("Repository or GitHub installation not found");
        }
    
        $accessToken = optional($repository->githubInstallation)->access_token;
        
        if (!$accessToken) {
            logger('[ERROR] Access token not found', ['repository' => $repository]);
            throw new \Exception("Access token not found");
        }

        return $accessToken;
    }

    private static function handleIssue(array $issuePayload, ?string $action)
    {
        $issueUrl = $issuePayload['html_url'];
        Issue::where('github_url', $issueUrl)->update(['state' => $action === "reopened" ? "open" : $action]);
    }

    private static function processDonations(Issue $issue, User $dbUser)
    {
        $today = Carbon::now()->toDateString();
        $donationsSumAmount = Donation::where('donatable_id', $issue->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', $today);
            })
            ->where('paid', false)
            ->sum('amount');

        $destinationStripeId = $dbUser->stripe_id;

        if (isset($destinationStripeId)) {
            self::transferFunds($destinationStripeId, $donationsSumAmount, $issue->id, $dbUser->email, $dbUser->name);
        } else {
            logger('[WARNING] Cannot transfer funds: User does not have a connected Stripe account.', ['user_id' => $dbUser->id]);
            // TODO: Send email to notify the user to connect Stripe account to OpenPledge
        }

        SendIssueResolverMail::send($dbUser->email, $dbUser->name, $issue->id); // Send emails to all resolvers during beta, regardless of Stripe connection
    }

    private static function transferFunds(string $destinationStripeId, $amount, int $issueId, string $resolverMail, string $resolverName)
    {
        if ($amount <= 0) {
            return;
        }

        Stripe::setApiKey(config('app.stripe_secret'));

        try {
            $transfer = Transfer::create([
                'amount' => $amount * 100, // Multiplied by 100 because Stripe expects the amount in cents
                'currency' => 'eur',
                'destination' => $destinationStripeId
            ]);

            logger('[INFO] Funds transferred', ['issue_id' => $issueId, 'amount' => $amount, 'stripe_transfer_id' => $transfer->id]);

            Donation::where('donatable_id', $issueId)->update(['paid' => true]);
        } catch (\Exception $e) {
            logger('[ERROR] Stripe Transfer failed', ['error' => $e->getMessage(), 'issue_id' => $issueId]);
            throw $e;
        }
    }
}
