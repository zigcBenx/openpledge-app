<?php

namespace App\Actions\Search;

use App\Models\Issue;
use App\Services\GithubService;

class SearchIssues
{
    public static function search($totalResultsLimit = 5, $searchQuery, $includeGitHubResults = false)
    {
        $localResults = self::fetchLocalIssues($totalResultsLimit, $searchQuery, $includeGitHubResults);
        $results = $localResults->toArray();

        if ($includeGitHubResults && $localResults->count() < $totalResultsLimit) {
            $githubResults = GithubService::getIssuesBySearchQuery($searchQuery, $totalResultsLimit - $localResults->count(), $localResults);
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
}
