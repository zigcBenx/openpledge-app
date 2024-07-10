<?php

namespace App\Actions\Github;

use Illuminate\Support\Facades\Http;

class GetGithubUser
{
    public static function getByGithubId($github_id)
    {
        $url = "https://api.github.com/user/{$github_id}";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
