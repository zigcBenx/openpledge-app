<?php

namespace App\Actions\Repository;

use App\Actions\Github\GetGithubRepositoryByName;

class ConnectRepository
{
    public static function connect($request)
    {
        $repository = $request['repository'];
        list($username, $repo) = explode('/', $repository);
        $githubRepo = GetGithubRepositoryByName::run($username, $repo);

        return CreateNewRepository::create([
            'title' => $githubRepo['full_name'],
            'github_url' => $githubRepo['html_url'],
            'github_id' => $githubRepo['id'],
            'user_avatar' => $githubRepo['owner']['avatar_url'],
        ]);
    }
}
