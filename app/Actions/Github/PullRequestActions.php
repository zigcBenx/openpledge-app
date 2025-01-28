<?php

namespace App\Actions\Github;

use App\Services\GithubService;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Traits\LoadsGraphQLQueries;

class PullRequestActions
{

    public static function getConnectedPullRequests($repositoryOwner, $repositoryName, $githubIssueNumber)
    {
        $query = LoadsGraphQLQueries::loadGraphQLQuery('get-connected-pull-requests');

        $repositoryUrl = "https://github.com/{$repositoryOwner}/{$repositoryName}";
        $accessToken = AuthActions::getAccessTokenByRepositoryUrl($repositoryUrl);

        return GithubService::executeGraphQLQuery($accessToken, $query, [
            'repoOwner' => $repositoryOwner,
            'repoName' => $repositoryName,
            'issueNumber' => $githubIssueNumber,
        ]);
    }

    public static function getPullRequestData($repositoryOwner, $repositoryName, $pullRequestNumber)
    {
        $repositoryUrl = "https://github.com/{$repositoryOwner}/{$repositoryName}";
        $token = AuthActions::getAccessTokenByRepositoryUrl($repositoryUrl);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ])->get(GithubService::BASE_URL . "/repos/{$repositoryOwner}/{$repositoryName}/pulls/{$pullRequestNumber}");

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception('Failed to fetch pull request data, response: ' . $response->body());
    }
}

