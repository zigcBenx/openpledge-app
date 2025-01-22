<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use Illuminate\Http\Request;

class GithubController extends Controller
{

    public function handleGithubAuthRedirect()
    {
        return GithubService::handleGithubAuthRedirect();
    }

    public function handleGithubAuthCallback()
    {
        return GithubService::handleGithubAuthCallback();
    }

    public function handleGithubAppCallback(Request $request)
    {
        return GithubService::handleGithubAppCallback($request);
    }

    public function handleGithubAppWebhook(Request $request)
    {
        return GithubService::handleGithubAppWebhook($request);
    }
}
