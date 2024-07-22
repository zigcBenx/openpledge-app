<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use App\Models\Repository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils as PromiseUtils;

class GetIssues
{
    public static function get()
    {
        return Issue::withSum('donations', 'amount', 'programmingLanguages')->get();
    }

    public static function getWithActiveDonations($filters = null, $offset = 0, $limit = 10)
    {
        $today = Carbon::now()->toDateString();

        $query = Issue::query()
            ->with('programmingLanguages', 'repository')
            ->withSum([
                'donations' => function ($query) use ($today) {
                    $query->where(function ($query) use ($today) {
                        $query->whereNull('expire_date')
                            ->orWhere('expire_date', '>', $today);
                    });
                }
            ], 'amount')
            ->having('donations_sum_amount', '>', 0)
            ->having('state', 'open');

        if ($filters) {
            if (isset($filters['range'])) {
                list($minRange, $maxRange) = explode('-', $filters['range']);
                $query->having('donations_sum_amount', '>=', (int) $minRange)
                    ->having('donations_sum_amount', '<=', (int) $maxRange);
            }

            if (isset($filters['date'])) {
                list($month, $year) = explode('-', $filters['date']);
                $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }

            if (isset($filters['languages'])) {
                $languagesArray = explode(',', $filters['languages']);
                $query->whereHas('programmingLanguages', function ($query) use ($languagesArray) {
                    $query->whereIn('name', $languagesArray);
                });
            }

            if (isset($filters['labels'])) {
                $labelsArray = explode(',', $filters['labels']);
                $query->whereIn('labels', $labelsArray);
            }
        }

        $query = $query->skip($offset)->take($limit);

        return $query->get();
    }

    public static function getRepositoryConnectedIssues($neededIssues, $existingIssues)
    {
        $connectedRepositories = Repository::inRandomOrder()->get();
        $allConnectedIssues = [];

        $client = new Client([
            'base_uri' => 'https://api.github.com',
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.github.personal_access_token')
            ]
        ]);

        $batchSize = 25; // Maximum number of concurrent requests
        $totalIssuesCollected = 0;

        foreach (array_chunk($connectedRepositories->all(), $batchSize) as $repositoriesBatch) {
            $promises = [];
            foreach ($repositoriesBatch as $connectedRepository) {
                $url = "/repos/{$connectedRepository['title']}/issues";
                $promises[] = [
                    'promise' => $client->getAsync($url),
                    'repository' => $connectedRepository,
                ];
            }

            $results = PromiseUtils::settle(array_column($promises, 'promise'))->wait();

            foreach ($results as $index => $result) {
                $repository = $promises[$index]['repository'];
                if ($result['state'] === 'fulfilled' && $result['value']->getStatusCode() === 200) {
                    $issues = json_decode($result['value']->getBody()->getContents(), true);
                    foreach ($issues as $issue) {
                        if (!strpos($issue['html_url'], '/pull/') && !in_array($issue['html_url'], $existingIssues)) {
                            $allConnectedIssues[] = [
                                'title' => $issue['title'],
                                'github_url' => $issue['html_url'],
                                'id' => $issue['id'],
                                'repository' => $repository,
                                'user_avatar' => $issue['user']['avatar_url'],
                                'github_username' => $issue['user']['login'],
                                'github_created_at' => $issue['created_at'],
                                'isExternal' => true,
                                'state' => $issue['state']
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
}
