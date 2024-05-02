<?php

namespace App\Http\Controllers;

use App\Actions\Github\GetGithubRepositories;
use App\Actions\Repository\CreateNewRepository;
use App\Actions\Repository\GetRepositories;
use App\Actions\Repository\GetRepositoryById;
use App\Actions\Repository\GetRepositoryByTitle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepositoryController extends Controller
{
    public function getRequestNew(Request $request)
    {                
        return Inertia::render('Repositories/Create');
    }

    public function store(Request $request)
    {
        return CreateNewRepository::create($request->all());
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
            $githubResult = GetGithubRepositories::run('repo:' . $githubUser . '/' . $repositoryName);
            if (!$githubResult['total_count']) {
                logger("Fail to find");
                return;
            }
            $githubRepo = $githubResult['items'][0];

            $repository = [
                'title'              => $githubRepo['full_name'],
                'github_url'         => $githubRepo['html_url'],
                'github_id'          => $githubRepo['id'],
                'user_avatar'        => $githubRepo['owner']['avatar_url'],
                'direct_from_github' => true
            ];
        }

        return Inertia::render('Repositories/Show', [
            'repository' => $repository
        ]);
    }
}