<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use App\Actions\Repository\CreateNewRepository;
use App\Actions\Repository\GetRepositories;
use App\Actions\Repository\GetRepositoryByTitle;
use App\Actions\Repository\UpdateRepositorySettings;
use App\Actions\Issue\GetIssuesByName;
use App\Http\Requests\CreateNewRepositoryRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RepositoryController extends Controller
{
    public function getRequestNew(Request $request)
    {
        return Inertia::render('Repositories/Create');
    }

    public function store(CreateNewRepositoryRequest $request)
    {
        return CreateNewRepository::create($request->validated());
    }

    public function index()
    {
        $repositories = GetRepositories::get();
        return Inertia::render('Repositories/Index', [
            'repositories' => $repositories
        ]);
    }

    /**
     * This function searches database for matching repository,
     * if none is found it tries searching Github database.
     * Display differs in displaying connect button.
     */
    public function show($githubUser, $repositoryName)
    {
        $repository = GetRepositoryByTitle::get($githubUser . '/' . $repositoryName);

        if (!$repository) {
            try {
                $githubRepo = GithubService::getRepositoryByName($githubUser, $repositoryName);
            } catch(Exception $e) {
                $isAuthenticated = Auth::check();
                return Inertia::render('Error', [
                    'message' => 'Repository access failed. It might be private. Connect with our GitHub app for seamless access to your repositories.',
                    'subMessage' => view('instructions.connect_repository_instructions')->render(),
                    'redirectUrl' => $isAuthenticated ? config('services.github.app_installation_url') : route('login'),
                    'redirectButtonText' => 'Connect',
                    'actionUrl' => $isAuthenticated ? route('save-redirect-path') : null,
                    'actionData' => $isAuthenticated ? [
                        'redirect_path' => route(
                            'repositories.show',
                            ['githubUser' => $githubUser, 'repository' => $repositoryName],
                            false
                        ),
                        'redirect_path_key' => 'github_redirect_path'
                    ] : null
                ]);
            }

            $repository = [
                'title'              => $githubRepo['full_name'],
                'github_url'         => $githubRepo['html_url'],
                'github_id'          => $githubRepo['id'],
                'user_avatar'        => $githubRepo['owner']['avatar_url'],
                'direct_from_github' => true,
                'owner_type'         => $githubRepo['owner']['type'],
                'owner_id'           => (int) $githubRepo['owner']['id']
            ];

            $issues = GetIssuesByName::get($githubUser, $repositoryName, null);
        } else {
            $issues = GetIssuesByName::get($githubUser, $repositoryName, $repository->github_installation_id);
        }

        $authenticatedUser = Auth::user();
        $isGithubAppConnected = $authenticatedUser?->hasGitHubAppInstalled() ?? false;
        $isRepositoryOwner = self::hasPermissionsForRepository($repository, $authenticatedUser);

        return Inertia::render('Repositories/Show', [
            'repository' => $repository,
            'issues' => array_values(collect($issues)->filter(function ($issue) {
                return $issue instanceof \App\Models\Issue ? $issue->state !== 'closed' : true;
            })->all()),
            'isRepositoryOwner' => $isRepositoryOwner,
            'isGithubAppConnected' => $isGithubAppConnected
        ]);
    }

    private static function hasPermissionsForRepository($repository, $user): bool
    {
        if ($repository['owner_type'] === 'Organization') {
            $token = Auth::user()->github_token;
            $hasAccessToOrganization = GithubService::hasAccessToRepositoryOrganization($repository['title'], $token);
            if ($hasAccessToOrganization) {
                return true;
            }
        }
        return isset($user, $repository['owner_id']) && $repository['owner_id'] === (int) $user->github_id;
    }

    public function updateSettings(Request $request, $repositoryId)
    {
        $validated = $request->validate([
            'require_pledgeable_label' => 'boolean',
            'allowed_labels' => 'nullable|array',
            'allowed_labels.*' => 'string',
            'enable_donation_expiry' => 'boolean',
            'default_expiry_days' => 'nullable|integer|min:1|max:365',
            'min_donation_amount' => 'nullable|numeric|min:0.01',
            'max_donation_amount' => 'nullable|numeric|min:0.01',
        ]);

        try {
            $settings = UpdateRepositorySettings::update($repositoryId, $validated);

            return response()->json([
                'message' => 'Repository settings updated successfully.',
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
