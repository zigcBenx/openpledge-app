<?php

namespace App\Actions\Github;

use App\Actions\Payment\ProcessPayment;
use App\Http\Requests\CreateNewRepositoryRequest;
use App\Models\ProgrammingLanguage;
use App\Models\Repository;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\GitHubInstallation;
use App\Actions\Repository\CreateNewRepository;
use App\Services\GitHubService;
use App\Models\Issue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AppActions
{
    public static function handleCallback(Request $request)
    {
        $code = $request->input('code');
        $installationId = $request->input('installation_id');
        $setupAction = $request->input('setup_action');

        $clientId = config('services.github.app_client_id');
        $clientSecret = config('services.github.app_client_secret');
        $redirectUri = config('services.github.app_callback');

        try {
            $response = Http::asForm()->post('https://github.com/login/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

            if (!$response->successful()) {
                logger('[ERROR] Failed to authenticate with GitHub.', ['response' => $response->body()]);
                return redirect('/error');
            }

            $data = [];
            parse_str($response->body(), $data);
            $accessToken = $data['access_token'];
        } catch (\Exception $e) {
            logger('[ERROR] Failed to fetch GitHub access token: ' . $e->getMessage());
            return redirect('/error');
        }

        DB::beginTransaction();
        try {
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
                $userAvatar = $repository['owner']['avatar_url'];

                if (strlen($userAvatar) > 255) {
                    [$owner, $repositoryTitle] = explode('/', $repository['full_name']);
                    $userAvatar = "https://ui-avatars.com/api/?name=$repositoryTitle&color=7F9CF5&background=EBF4FF";
                }

                $repositoryData = [
                    'title' => $repository['full_name'],
                    'github_url' => $repository['html_url'],
                    'github_id' => $repository['id'],
                    'user_avatar' => $userAvatar,
                    'user_id' => $user->id,
                    'github_installation_id' => $installationId
                ];

                $validator = Validator::make($repositoryData, (new CreateNewRepositoryRequest)->rules());

                if ($validator->fails()) {
                    logger("[ERROR] Validation failed for repository {$repository['full_name']}", [
                        'errors' => $validator->errors(),
                        'repository_data' => $repositoryData,
                    ]);
                    return redirect('/error');
                }

                $createdRepository = CreateNewRepository::create($repositoryData);

                $programmingLanguages = array_keys(GithubService::getRepositoryProgrammingLanguages($repository['full_name'], $accessToken));

                $languageIds = [];
                foreach ($programmingLanguages as $programmingLanguage) {
                    $language = ProgrammingLanguage::updateOrCreate(
                        ['name' => $programmingLanguage],
                        ['name' => $programmingLanguage]
                    );
                    $languageIds[] = $language->id;
                }

                $createdRepository->programmingLanguages()->sync($languageIds);
            }

            DB::commit();

            $redirectPath = session('github_redirect_path');

            if ($redirectPath) {
                session()->forget('github_redirect_path');

                $fullNameParts = explode('/', $redirectPath);

                $githubUser = $fullNameParts[2];
                $repository = $fullNameParts[3];
    
                return redirect(route('repositories.show', [
                    'githubUser' => $githubUser,
                    'repository' => $repository,
                ]));
            }

            return redirect(route('discover.issues'));
        } catch (\Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed: ' . $e->getMessage());
            return redirect('/error');
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
                self::handleWebhookIssue($payload, $action);
            } elseif (isset($payload['repository'])) {
                self::handleWebhookRepository($payload);
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
        $issueHeadData = $pullRequest['base'];
        $repositoryData = $issueHeadData['repo'];

        $isPullRequestMerged = ($pullRequest['merged'] ?? false) === true;

        if (!$isPullRequestMerged) {
            return;
        }

        $issueEventsUrl = str_replace('{/number}', '', $repositoryData['issue_events_url']);
        $issueEventsResponse = Http::withToken(AuthActions::getAccessTokenByRepositoryUrl($repositoryData['html_url']))->get($issueEventsUrl);
        $issueEvents = $issueEventsResponse->json();

        $closedIssueUrl = null;

        foreach ($issueEvents as $issueEvent) {
            if (isset($issueEvent["event"]) && $issueEvent["event"] === "closed") {
                $closedIssueUrl = $issueEvent["issue"]["html_url"];
                break;
            }
        }

        $issue = Issue::where('github_url', $closedIssueUrl)->firstOrFail();
        $user = $pullRequest['user'];

        $issue->state = $action === "reopened" ? "open" : $action;
        $issue->resolver_github_id = $user['id'];
        $issue->resolved_at = Carbon::parse($pullRequest['merged_at'])
            ->setTimezone(config('app.timezone'));
        $issue->save();

        $dbUser = User::where('github_id', $user['id'])->first();

        if ($dbUser) {
            ProcessPayment::processDonations($issue, $dbUser);
        } else {
            logger('[WARNING] No user with the following GitHub id is connected to our app', ['id' => $user['id']]);
            // TODO: Send email to notify the user to register to OpenPledge & connect with Stripe to claim the reward
        }
    }

    private static function handleWebhookRepository($payload)
    {
        $repositoryId = $payload['repository']['id'];
        $repository = Repository::where('github_id', $repositoryId)->firstOrFail();

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

        $repository->programmingLanguages()->delete();
        $repository->programmingLanguages()->sync($languageIds);
        $repository->save();
    }

    private static function handleWebhookIssue($payload, $action)
    {
        $issueGithubId = $payload['issue']['id'];
        $issue = Issue::where('github_id', $issueGithubId)->firstOrFail();
        $issue->state = $action === "closed" ? "closed" : "open";
        $issue->title = $payload['issue']['title'];
        $issue->description = $payload['issue']['body'];
        $issue->labels()->delete();

        $allowedLabels = Label::$allowedLabels;
        $labels = $payload['issue']['labels'];

        foreach ($labels as $label) {
            if (in_array(strtolower($label['name']), $allowedLabels)) {
                $issue->labels()->create([
                    'name' => strtolower($label['name'])
                ]);
            }
        }

        $issue->save();
    }

    public static function saveRedirectPath($request)
    {
        $redirectPath = $request->input('redirect_path');
        session(['github_redirect_path' => $redirectPath]);
    }
}
