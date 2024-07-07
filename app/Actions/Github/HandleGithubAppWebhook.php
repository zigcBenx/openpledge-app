<?php

namespace App\Actions\Github;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        Log::info('HandleGithubAppWebhook run method started');

        $payload = $request->all();

        DB::beginTransaction();
        try {
            $action = $payload['action'] ?? null;
            $pullRequest = $payload['pull_request'] ?? null;

            if ($pullRequest && isset($pullRequest['merged']) && $pullRequest['merged']) {
                Log::info('Handling pull request', ['action' => $action]);
                self::handlePullRequest($pullRequest, $action);
            } elseif (isset($payload['issue'])) {
                Log::info('Handling issue', ['action' => $action]);
                self::handleIssue($payload['issue'], $action);
            }

            DB::commit();
            Log::info('Transaction committed');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Transaction failed'], 500);
        }

        Log::info('HandleGithubAppWebhook run method completed successfully');
        return response()->json(['status' => 'success'], 200);
    }

    private static function handlePullRequest(array $pullRequest, ?string $action)
    {
        Log::info('handlePullRequest method started', ['action' => $action]);

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

        Log::info('Closed issue URL found', ['closed_issue_url' => $closedIssueUrl]);

        $issue = Issue::where('github_url', $closedIssueUrl)->firstOrFail();
        $user = $pullRequest['user'];

        $issue->state = $action === "reopened" ? "open" : $action;
        $issue->save();

        $dbUser = User::where('github_id', $user['id'])->first();

        if ($dbUser) {
            Log::info('Processing donations', ['issue_id' => $issue->id, 'user_id' => $dbUser->id]);
            self::processDonations($issue, $dbUser);
        } else {
            Log::warning('No user with the following GitHub id is connected to our app', ['id' => $user['id']]);
            // TODO: Send email to notify the user to register to OpenPledge & connect with Stripe to claim the reward
        }

        Log::info('handlePullRequest method completed');
    }

    private static function getAccessToken($repoUrl)
    {
        Log::info('getAccessToken method started', ['repo_url' => $repoUrl]);

        $repository = Repository::with('githubInstallation')
        ->where('github_url', $repoUrl)
        ->first();

        if (!$repository) {
            Log::error('Repository or GitHub installation not found', ['repo_url' => $repoUrl]);
            throw new \Exception("Repository or GitHub installation not found");
        }
    
        $accessToken = optional($repository->githubInstallation)->access_token;
        
        if (!$accessToken) {
            Log::error('Access token not found', ['repository' => $repository]);
            throw new \Exception("Access token not found");
        }
    
        Log::info('getAccessToken method completed', ['access_token' => $accessToken]);
        return $accessToken;
    }

    private static function handleIssue(array $issuePayload, ?string $action)
    {
        Log::info('handleIssue method started', ['action' => $action]);

        $issueUrl = $issuePayload['html_url'];
        Issue::where('github_url', $issueUrl)->update(['state' => $action === "reopened" ? "open" : $action]);

        Log::info('handleIssue method completed', ['issue_url' => $issueUrl]);
    }

    private static function processDonations(Issue $issue, User $dbUser)
    {
        Log::info('processDonations method started', ['issue_id' => $issue->id, 'user_id' => $dbUser->id]);

        $today = Carbon::now()->toDateString();
        $donationsSumAmount = Donation::where('donatable_id', $issue->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', $today);
            })
            ->where('paid', false)
            ->sum('amount');

        Log::info('Total donations amount for issue', ['issue_id' => $issue->id, 'amount' => $donationsSumAmount]);

        $destinationStripeId = $dbUser->stripe_id;

        if (isset($destinationStripeId)) {
            Log::info('User has Stripe account, transferring funds', ['user_id' => $dbUser->id]);
            self::transferFunds($destinationStripeId, $donationsSumAmount, $issue->id);
        } else {
            Log::warning('User does not have a Stripe account connected', ['user_id' => $dbUser->id]);
            // TODO: Send email to notify the user to connect Stripe account to OpenPledge
        }

        Log::info('processDonations method completed');
    }

    private static function transferFunds(string $destinationStripeId, $amount, int $issueId)
    {
        Log::info('transferFunds method started', ['issue_id' => $issueId, 'amount' => $amount]);

        if ($amount <= 0) {
            Log::info('No funds to transfer for issue', ['issue_id' => $issueId]);
            return;
        }

        Stripe::setApiKey(config('app.stripe_secret'));

        try {
            $transfer = Transfer::create([
                'amount' => $amount * 100, // Multiplied by 100 because Stripe expects the amount in cents
                'currency' => 'eur',
                'destination' => $destinationStripeId
            ]);

            Log::info('Funds transferred', ['issue_id' => $issueId, 'amount' => $amount, 'stripe_transfer_id' => $transfer->id]);
            // TODO: Send email to notify the user that the funds were transferred to his account, include the issue & pull request information

            Donation::where('donatable_id', $issueId)->update(['paid' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe Transfer failed', ['error' => $e->getMessage(), 'issue_id' => $issueId]);
            throw $e;
        }

        Log::info('transferFunds method completed');
    }
}
