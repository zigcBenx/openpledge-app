<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use App\Actions\Repository\CreateNewRepository;
use App\Actions\Repository\GetRepositories;
use App\Actions\Repository\GetRepositoryByTitle;
use App\Actions\Issue\GetIssuesByName;
use App\Http\Requests\CreateNewRepositoryRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
                return Redirect::route('error', ['any' => 'error']);
            }
            $repository = [
                'title'              => $githubRepo['full_name'],
                'github_url'         => $githubRepo['html_url'],
                'github_id'          => $githubRepo['id'],
                'user_avatar'        => $githubRepo['owner']['avatar_url'],
                'direct_from_github' => true,
                'owner_id' => (int) $githubRepo['owner']['id']
            ];

            $issues = GetIssuesByName::get($githubUser, $repositoryName, null);
        } else {
            $issues = GetIssuesByName::get($githubUser, $repositoryName, $repository->github_installation_id);
        }

        $authenticatedUser = Auth::user();
        $isGithubAppConnected = $authenticatedUser?->hasGitHubAppInstalled() ?? false;
        $isRepositoryOwner = isset($authenticatedUser, $repository['owner_id']) && $repository['owner_id'] === (int) $authenticatedUser->github_id;

        return Inertia::render('Repositories/Show', [
            'repository' => $repository,
            'issues' => array_values(collect($issues)->filter(function ($issue) {
                return $issue instanceof \App\Models\Issue ? $issue->state !== 'closed' : true;
            })->all()),
            'isRepositoryOwner' => $isRepositoryOwner,
            'isGithubAppConnected' => $isGithubAppConnected
        ]);
    }
}