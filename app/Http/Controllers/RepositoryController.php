<?php

namespace App\Http\Controllers;

use App\Actions\Github\GetGithubRepositoryByName;
use App\Actions\Repository\ConnectRepository;
use App\Actions\Repository\CreateNewRepository;
use App\Actions\Repository\GetRepositories;
use App\Actions\Repository\GetRepositoryByTitle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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

    public function connect(Request $request)
    {
        return ConnectRepository::connect($request->all());
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
                $githubRepo = GetGithubRepositoryByName::run($githubUser, $repositoryName);
            } catch(Exception $e) {
                return Redirect::route('error', ['any' => 'error']);
            }
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