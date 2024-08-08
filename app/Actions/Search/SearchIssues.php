<?php

namespace App\Actions\Search;

use App\Models\GitHubInstallation;
use App\Models\Issue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchIssues
{
    public static function search($totalResultsLimit = 5, $searchQuery, $includeGitHubResults = false)
    {
        $localResults = self::fetchLocalIssues($totalResultsLimit, $searchQuery, $includeGitHubResults);
        $results = $localResults->toArray();

        if ($includeGitHubResults && $localResults->count() < $totalResultsLimit) {
            $githubResults = self::fetchGitHubIssues($searchQuery, $totalResultsLimit - $localResults->count(), $localResults);
            $results = array_merge($results, $githubResults);
        }

        return $results;
    }

    private static function fetchLocalIssues($totalResultsLimit, $searchQuery, $includeGitHubResults)
    {
        $resultsToFetch = $includeGitHubResults ? ceil($totalResultsLimit / 2) : $totalResultsLimit;

        return Issue::where('title', 'LIKE', "%$searchQuery%")
            ->take($resultsToFetch)
            ->get(['id', 'title', 'github_url']);
    }

    private static function fetchGitHubIssues($searchQuery, $resultsToFetch, $localResults)
    {
        $accessToken = GitHubInstallation::getRandomAccessToken();
        $searchQuery = "is:issue is:public $searchQuery";

        try {
            $response = Http::withToken($accessToken)
                ->get('https://api.github.com/search/issues', [
                    'q' => $searchQuery,
                    'per_page' => $resultsToFetch
                ]);

            $githubResults = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Error fetching GitHub issues: ' . $e->getMessage());
            return [];
        }

        if (!isset($githubResults['items'])) {
            return [];
        }

        $localIssueGithubUrls = $localResults->pluck('github_url')->toArray();

        return collect($githubResults['items'])
            ->reject(function ($issue) use ($localIssueGithubUrls) {
                return in_array($issue['html_url'], $localIssueGithubUrls);
            })
            ->map(function ($issue) {
                $repositoryUrlParts = explode('/', $issue['repository_url']);
                $repositoryFullName = $repositoryUrlParts[4] . '/' . $repositoryUrlParts[5];

                return [
                    'id' => $issue['id'],
                    'title' => $issue['title'],
                    'repository_title' => $repositoryFullName
                ];
            })
            ->toArray();
    }
}
