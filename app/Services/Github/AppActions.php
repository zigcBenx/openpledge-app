<?php

namespace App\Services\Github;

use App\Http\Requests\CreateNewRepositoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\GitHubInstallation;
use App\Actions\Repository\CreateNewRepository;
use App\Services\Github\GitHubService;
use App\Models\Issue;
use App\Models\User;
use Carbon\Carbon;

class AppActions
{
    public static function handleCallback(Request $request)
    {
        DB::beginTransaction();

        try {
            $code = $request->input('code');
            $installationId = $request->input('installation_id');
            $setupAction = $request->input('setup_action');

            $clientId = config('services.github.app_client_id');
            $clientSecret = config('services.github.app_client_secret');
            $redirectUri = config('services.github.app_callback');

            $response = Http::asForm()->post('https://github.com/login/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

            if ($response->successful()) {
                $data = [];
                parse_str($response->body(), $data);

                $accessToken = $data['access_token'];

                // Fetch repositories for the specific installation
                $githubRepositoriesData = GithubService::getRepositoriesByInstallationId($installationId, $accessToken);

                $user = Auth::user();
                GitHubInstallation::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'installation_id' => $installationId,
                        'setup_action' => $setupAction,
                        'access_token' => $accessToken,
                    ]
                );

                foreach ($githubRepositoriesData as $repository) {
                    $validatedRepositoryData = app(CreateNewRepositoryRequest::class)->validate([
                        'title' => $repository['full_name'],
                        'github_url' => $repository['html_url'],
                        'github_id' => $repository['id'],
                        'user_avatar' => $repository['owner']['avatar_url'],
                        'user_id' => $user->id,
                        'github_installation_id' => $installationId
                    ]);

                    CreateNewRepository::create($validatedRepositoryData);
                }

                DB::commit();

                return redirect('/user/profile');
            } else {
                logger('[ERROR] Failed to authenticate with GitHub.', ['response' => $response->body()]);
                return redirect('/error');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed: ' . $e->getMessage());
            return redirect('/error')->with('error', 'An error occurred while processing your request.');
        }
    }

    public static function handleWebhook(Request $request)
    {
        $payload = $request->all();

        DB::beginTransaction();
        try {
            $action = $payload['action'] ?? null;
            $pullRequest = $payload['pull_request'] ?? null;

            if ($pullRequest && isset($pullRequest['merged']) && $pullRequest['merged']) {
                self::handleWebhookPullRequest($pullRequest, $action);
            } elseif (isset($payload['issue'])) {
                $issueUrl = $payload['issue']['html_url'];
                Issue::where('github_url', $issueUrl)->update(['state' => $action === "reopened" ? "open" : $action]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Transaction failed'], 500);
        }
        return response()->json(['status' => 'success'], 200);
    }

    private static function handleWebhookPullRequest(array $pullRequest, ?string $action)
    {
        $issueHeadData = $pullRequest['head'];
        $repositoryData = $issueHeadData['repo'];

        $eventsResponse = Http::withToken(AuthActions::getAccessTokenByRepositoryUrl($repositoryData['html_url']))->get($repositoryData['events_url']);
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
            RewardActions::processDonations($issue, $dbUser);
        } else {
            logger('[WARNING] No user with the following GitHub id is connected to our app', ['id' => $user['id']]);
            // TODO: Send email to notify the user to register to OpenPledge & connect with Stripe to claim the reward
        }
    }
}