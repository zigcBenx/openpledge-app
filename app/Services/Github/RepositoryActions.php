<?php

namespace App\Services\Github;

use Exception;
use Github\Exception\ValidationFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GrahamCampbell\GitHub\Facades\GitHub;

class RepositoryActions
{
    public static function getByInstallationId($installationId, $accessToken)
    {
        $url = GithubService::BASE_URL . "/user/installations/{$installationId}/repositories";

        $response = Http::withToken($accessToken)
            ->get($url);

        if ($response->successful()) {
            return $response->json()['repositories'] ?? [];
        } else {
            logger('[ERROR] Failed to fetch GitHub repositories', ['response' => $response->body(), 'accesstoken' => $accessToken, 'installationId' => $installationId]);
            return [];
        }
    }

    public static function getBySearchQuery($searchQuery, $resultsToFetch, $localResults)
    {
        $accessToken = AuthActions::getAccessTokenByAuthenticatedUser(Auth::user());
        $url = GithubService::BASE_URL . "/search/repositories";

        try {
            $response = Http::withToken($accessToken)
                ->get($url, [
                    'q' => $searchQuery,
                    'per_page' => $resultsToFetch
                ]);

            $githubResults = json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
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
                $repo = (array) $repo;
                return [
                    'id' => $repo['id'],
                    'title' => $repo['full_name']
                ];
            })
            ->toArray();
    }

    public static function getByName($githubUser, $repositoryName)
    {
        try {
            $githubResult = GitHub::search()->repositories("repo:$githubUser/$repositoryName", 1);
            if (!$githubResult['total_count']) {
                throw new Exception('This repository is private or it doesn\'t exist on Github!');
            }
            return $githubResult['items'][0];
        } catch (ValidationFailedException $e) {
            throw new Exception('The specified user or repository does not exist on GitHub!', $e->getCode(), $e);
        } catch (Exception $e) {
            throw $e;
        }
    }
}