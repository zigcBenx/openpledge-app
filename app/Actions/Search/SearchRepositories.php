<?php

namespace App\Actions\Search;

use App\Models\GitHubInstallation;
use App\Models\Repository;
use Illuminate\Support\Facades\Http;

class SearchRepositories
{
    public static function search($totalResultsLimit = 5, $searchQuery, $includeGitHubResults = false)
    {
        $localResults = self::fetchLocalRepositories($totalResultsLimit, $searchQuery, $includeGitHubResults);
        $results = $localResults->toArray();

        if ($includeGitHubResults && $localResults->count() < $totalResultsLimit) {
            $githubResults = self::fetchGitHubRepositories($searchQuery, $totalResultsLimit - $localResults->count(), $localResults);
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

    private static function fetchGitHubRepositories($searchQuery, $resultsToFetch, $localResults)
    {
        $accessToken = GitHubInstallation::getRandomAccessToken();

        try {
            $response = Http::withToken($accessToken)
                ->get('https://api.github.com/search/repositories', [
                    'q' => $searchQuery,
                    'per_page' => $resultsToFetch
                ]);

            $githubResults = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            logger('[ERROR] Error fetching GitHub repositories: ' . $e->getMessage());
        }

        if (!isset($githubResults['items'])) {
            return [];
        }

        $localTitles = $localResults->pluck('title')->toArray();

        return collect($githubResults['items'])
            ->reject(function ($repo) use ($localTitles) {
                return in_array($repo['full_name'], $localTitles);
            })
            ->map(function ($repo) {
                return [
                    'id' => $repo['id'],
                    'title' => $repo['full_name']
                ];
            })
            ->toArray();
    }
}
