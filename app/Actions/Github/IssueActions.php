<?php

namespace App\Actions\Github;

use App\Models\Repository;
use App\Services\GithubService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils as PromiseUtils;
use App\Models\Label;
use Carbon\Carbon;

class IssueActions
{
    public static function comment($installationId, $owner, $repo, $issueNumber, $comment)
    {
        $url = GithubService::BASE_URL . "/repos/{$owner}/{$repo}/issues/{$issueNumber}/comments";
        $token = AuthActions::generateAppInstallationAccessToken($installationId);

        $response = Http::withHeaders([
            'Authorization' => "token {$token}",
            'Accept' => 'application/vnd.github+json',
        ])->post($url, [
                    'body' => $comment,
                ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            logger('[ERROR] Failed to post comment on GitHub issue: ', ['response' => $response->body()]);
        }
    }

    public static function getActivityTimeline($issueGithubUrl, $githubAccessToken, $donations, $resolver, $resolvedAt)
    {
        if (!preg_match('/^https:\/\/github\.com\/([\w\-]+)\/([\w\-]+)\/issues\/(\d+)$/', $issueGithubUrl, $matches)) {
            logger("[WARNING] Invalid GitHub issue URL provided: {$issueGithubUrl}");
            return null;
        }

        $user = $matches[1];
        $repo = $matches[2];
        $issueNumber = $matches[3];

        $url = GithubService::BASE_URL . "/repos/{$user}/{$repo}/issues/{$issueNumber}/timeline";
        $response = Http::withToken($githubAccessToken)->get($url);

        if ($response->successful()) {
            $activities = $response->json();

            if (isset($donations)) {
                $activities = array_merge($activities, $donations->toArray());
            }

            if (isset($resolver) && isset($resolvedAt)) {
                $activities = array_merge($activities, [
                    [
                        'actor' => [
                            'avatar_url' => $resolver['avatar_url'],
                            'login' => $resolver['login'],
                        ],
                        'event' => 'resolved',
                        'created_at' => $resolvedAt,
                    ]
                ]);
            }

            // Get the latest activities first
            usort($activities, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return $activities;
        } else {
            logger('[ERROR] Failed to fetch issue activity from GitHub: ' . $response->body());
            return null;
        }
    }

    public static function getConnectedInBatch($neededIssues, $existingIssues, $filters)
    {
        // if user filters issues by programming languages, get repositories with only those programming languages
        if (!empty($filters['languages'])) {
            $filters['languages'] = explode(',', $filters['languages']);
            $connectedRepositories = Repository::whereHas('programmingLanguages', function ($query) use ($filters) {
                $query->whereIn('name', $filters['languages']);
            })->inRandomOrder()->with('programmingLanguages')->get();
        } else {
            $connectedRepositories = Repository::inRandomOrder()->with('programmingLanguages')->get();
        }
        $allConnectedIssues = [];

        if ($connectedRepositories->isEmpty()) {
            return [];
        }

        $accessToken = AuthActions::getAccessTokenByAuthenticatedUser(Auth::user());

        $client = new Client([
            'base_uri' => GithubService::BASE_URL,
            'headers' => [
                'Authorization' => "Bearer $accessToken"
            ]
        ]);

        $batchSize = 25; // Maximum number of concurrent requests
        $totalIssuesCollected = 0;

        foreach (array_chunk($connectedRepositories->all(), $batchSize) as $repositoriesBatch) {
            $promises = [];
            foreach ($repositoriesBatch as $connectedRepository) {
                $url = "/repos/{$connectedRepository['title']}/issues";
                $queryParams = self::getQueryParamsForBatchSearch($filters);
            
                if (!empty($queryParams)) {
                    $url .= '?' . http_build_query($queryParams);
                }

                $promises[] = [
                    'promise' => $client->getAsync($url),
                    'repository' => $connectedRepository,
                ];
            }

            $results = PromiseUtils::settle(array_column($promises, 'promise'))->wait();

            $dateFilter = !empty($filters['date'])
                ? Carbon::createFromFormat('n-Y', $filters['date'], 'UTC')->startOfMonth()
                : null;

            foreach ($results as $index => $result) {
                $repository = $promises[$index]['repository'];
                if ($result['state'] === 'fulfilled' && $result['value']->getStatusCode() === 200) {
                    $issues = json_decode($result['value']->getBody()->getContents(), true);
                    foreach ($issues as $issue) {
                        if (
                            !strpos($issue['html_url'], '/pull/') &&
                            !in_array($issue['html_url'], $existingIssues) &&
                            (!$dateFilter || Carbon::parse($issue['created_at'])->gte($dateFilter))
                        ) {
                            $allConnectedIssues[] = [
                                'title' => $issue['title'],
                                'github_url' => $issue['html_url'],
                                'id' => $issue['id'],
                                'repository' => $repository,
                                'user_avatar' => $issue['user']['avatar_url'],
                                'github_username' => $issue['user']['login'],
                                'github_created_at' => $issue['created_at'],
                                'isExternal' => true,
                                'state' => $issue['state'],
                                'description' => $issue['body'],
                                'labels' => array_values(array_filter($issue['labels'], function ($label) {
                                    return in_array($label['name'], Label::$allowedLabels);
                                }))
                            ];
                            if (++$totalIssuesCollected >= $neededIssues) {
                                break 2; // Break out of both loops if needed issues are collected
                            }
                        }
                    }
                }
            }

            if ($totalIssuesCollected >= $neededIssues) {
                break; // Stop processing more batches if the needed number of issues is reached
            }
        }

        usort($allConnectedIssues, function ($a, $b) {
            return strtotime($b['github_created_at']) - strtotime($a['github_created_at']);
        });

        return $allConnectedIssues;
    }

    public static function getBySearchQuery($searchQuery, $resultsToFetch, $localResults)
    {
        $accessToken = AuthActions::getAccessTokenByAuthenticatedUser(Auth::user());
        $searchQuery = "is:issue is:public $searchQuery";

        $url = GithubService::BASE_URL . "/search/issues";

        try {
            $response = Http::withToken($accessToken)
                ->get($url, [
                    'q' => $searchQuery,
                    'per_page' => $resultsToFetch
                ]);

            $githubResults = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            logger('[ERROR] Error fetching GitHub issues: ' . $e->getMessage(), [
                'stack_trace' => $e->getTraceAsString()
            ]);
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
                $issue = (array) $issue;
                $repositoryUrlParts = explode('/', $issue['repository_url']);
                $repositoryFullName = "$repositoryUrlParts[4]/$repositoryUrlParts[5]";

                return [
                    'id' => $issue['id'],
                    'title' => $issue['title'],
                    'repository_title' => $repositoryFullName
                ];
            })
            ->toArray();
    }

    private static function getQueryParamsForBatchSearch($filters)
    {
        $queryParams = [];

        if (!empty($filters['labels'])) {
            $queryParams['labels'] = implode(',', $filters['labels']);
        }

        if (!empty($filters['languages'])) {
            // GitHub REST API does not support "languages" as a query param so we should filter the repositories by programming languages beforehand
        }

        if (!empty($filters['date'])) {
            // "since" query param filters by updated date, not created date, so we need to filter by created time manually afterwards
            $iso8601Date = Carbon::createFromFormat('n-Y', $filters['date'], 'UTC')->startOfMonth()->toIso8601String();
            $queryParams['since'] = $iso8601Date;
        }
    
        return $queryParams;
    }
}