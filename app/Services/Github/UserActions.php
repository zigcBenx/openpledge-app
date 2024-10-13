<?php

namespace App\Services\Github;

use Illuminate\Support\Facades\Http;

class UserActions
{
    public static function getByGithubId($github_id)
    {
        $url = GithubService::BASE_URL . "/user/{$github_id}";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}