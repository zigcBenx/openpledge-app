<?php

namespace App\Actions\Github;

use App\Actions\WalletTransaction\CreateNewWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Http,
    Validator
};

use App\Models\{
    GitHubInstallation,
    Issue,
    Label,
    ProgrammingLanguage,
    Repository,
    User
};

use App\Actions\Payment\ProcessPayment;
use App\Actions\Repository\CreateNewRepository;
use App\Services\GitHubService;

use App\Http\Requests\CreateNewRepositoryRequest;

use Carbon\Carbon;
use Exception;

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
        } catch (Exception $e) {
            logger('[ERROR] Failed to fetch GitHub access token: ' . $e->getMessage(), [
                'stack_trace' => $e->getTraceAsString()
            ]);
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
        } catch (Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed: ' . $e->getMessage(), [
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect('/error');
        }
    }

    public static function handleWebhook(Request $request)
    {
        $payload = $request->all();

        DB::beginTransaction();
        try {
            $action = $payload['action'] ?? null;

            if (isset($payload['issue'])) {
                self::handleWebhookIssue($payload, $action);
            } elseif (isset($payload['repository'])) {
                self::handleWebhookRepository($payload);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Transaction failed'], 500);
        }
        return response()->json(['status' => 'success'], 200);
    }

    private static function handleWebhookRepository($payload)
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

    private static function handleWebhookIssue($payload, $action)
    {
        $githubIssue = $payload['issue'];
        $issueGithubId = $githubIssue['id'];
        $issue = Issue::where('github_id', $issueGithubId)->first();
        if (!$issue) {
            return;
        }
        $issue->state = $action === "closed" ? "closed" : "open";
        $issue->title = $githubIssue['title'];
        $issue->description = $githubIssue['body'];
        $issue->labels()->delete();

        $allowedLabels = Label::$allowedLabels;
        $labels = $githubIssue['labels'];

        foreach ($labels as $label) {
            if (in_array(strtolower($label['name']), $allowedLabels)) {
                $issue->labels()->create([
                    'name' => strtolower($label['name'])
                ]);
            }
        }

        $issue->save();

        $isIssueWithDonations = $issue->donations->isNotEmpty();

        if ($action === "closed" && $isIssueWithDonations) {
            self::handleWebhookIssueClosed($payload, $issue, $githubIssue['number']);
        }
    }

    private static function handleWebhookIssueClosed($payload, $issue, $githubIssueNumber)
    {
        $repository = $payload['repository'];
        $repositoryOwner = $repository['owner']['login'];
        $repositoryName = $repository['name'];
        $mergedState = 'MERGED';

        $connectedPullRequestsResponse = GithubService::getConnectedPullRequests($repositoryOwner, $repositoryName, $githubIssueNumber);

        if ($connectedPullRequestsResponse) {
            $mergedPullRequest = collect($connectedPullRequestsResponse['data']['repository']['issue']['timelineItems']['nodes'])
                ->first(function ($item) {
                    // For ConnectedEvent
                    if (isset($item['subject'])) {
                        return $item['subject']['state'] === 'MERGED';
                    }
                    // For CrossReferencedEvent
                    if (isset($item['source'])) {
                        return $item['source']['state'] === 'MERGED';
                    }
                    return false;
                });

            if ($mergedPullRequest) {
                $prData = $mergedPullRequest['subject'] ?? $mergedPullRequest['source'];
                $mergedPullRequestNumber = $prData['number'];
                $mergedPullRequestData = GithubService::getPullRequestData($repositoryOwner, $repositoryName, $mergedPullRequestNumber);

                $resolverGithubId = $mergedPullRequestData['user']['id'];

                $issue->resolver_github_id = $resolverGithubId;
                $issue->resolved_at = Carbon::parse($mergedPullRequestData['merged_at'])
                    ->setTimezone(config('app.timezone'));
                $issue->save();

                $dbUser = User::where('github_id', $resolverGithubId)->first();

                if ($dbUser) {
                    CreateNewWalletTransaction::create($issue, $dbUser);
                    // ProcessPayment::processDonations($issue, $dbUser);
                } else {
                    logger('[WARNING] No user with the following GitHub id is connected to our app', ['id' => $resolverGithubId]);
                    // TODO: What to do here? We can't send email to the user to register to OpenPledge & connect with Stripe to claim the reward because the email is not publicly available
                }
            }
        }
    }
}
