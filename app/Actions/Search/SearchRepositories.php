<?php

namespace App\Actions\Search;

use App\Models\Repository;
use App\Services\Github\GitHubService;

class SearchRepositories
{
    public static function search($totalResultsLimit = 5, $searchQuery, $includeGitHubResults = false)
    {
        $localResults = self::fetchLocalRepositories($totalResultsLimit, $searchQuery, $includeGitHubResults);
        $results = $localResults->toArray();

        if ($includeGitHubResults && $localResults->count() < $totalResultsLimit) {
            $githubResults = GithubService::getRepositoriesBySearchQuery($searchQuery, $totalResultsLimit - $localResults->count(), $localResults);
            $results = array_merge($results, $githubResults);
        }

        return $results;
    }

    private static function fetchLocalRepositories($totalResultsLimit, $searchQuery, $includeGitHubResults)
    {
        $resultsToFetch = $includeGitHubResults ? ceil($totalResultsLimit / 2) : $totalResultsLimit;

        return Repository::where('title', 'LIKE', "%$searchQuery%")
            ->take($resultsToFetch)
            ->get(['id', 'title']);
    }
}
