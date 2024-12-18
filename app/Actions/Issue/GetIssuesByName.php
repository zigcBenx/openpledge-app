<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use App\Services\GithubService;
use App\Models\GitHubInstallation;
use Illuminate\Support\Facades\Http;

class GetIssuesByName
{
    public static function get($githubUser, $repositoryName, $repositoryGithubInstallationId, $state = null)
    {
        $pledgedIssues = Issue::where('github_url', 'LIKE', "https://github.com/$githubUser/$repositoryName/issues%")
            ->withSum('donations', 'amount')
            ->having('donations_sum_amount', '>', 0)
            ->when($state, function ($query, $state) {
                return $query->where('state', $state);
            })
            ->get();

        if (!isset($repositoryGithubInstallationId)) {
            return $pledgedIssues;
        }

        $installation = GitHubInstallation::where('installation_id', $repositoryGithubInstallationId)->first();

        $installedRepositories = GithubService::getRepositoriesByInstallationId($installation->installation_id, $installation->access_token);

        $repositoryData = array_filter($installedRepositories, function ($installedRepository) use ($githubUser, $repositoryName) {
            return strtolower($installedRepository['full_name']) == strtolower($githubUser . '/' . $repositoryName);
        });

        $repositoryData = array_values($repositoryData);
        $repositoryData = !empty($repositoryData) ? $repositoryData[0] : null;

        $githubIssues = self::getIssuesFromGitHub($repositoryData["issues_url"]);

        $filteredGithubIssues = array_filter($githubIssues, callback: function ($issue) use ($pledgedIssues) {
            return !in_array((string) $issue['id'], $pledgedIssues->pluck('github_id')->toArray());
        });

        $formattedGithubIssues = [];

        $formattedGithubIssues = array_map(function ($issue) {
            return [
                'id' => $issue['id'],
                'title' => $issue['title'],
                'github_url' => $issue['html_url'],
                'state' => $issue['state'],
                'labels' => $issue['labels'],
                'user_avatar' => $issue['user']['avatar_url'],
                'github_created_at' => $issue['created_at'],
                'isExternal' => true,
                'description' => $issue['body']
            ];
        }, $filteredGithubIssues);

        return array_values($formattedGithubIssues);
    }

    private static function getIssuesFromGitHub($issuesUrl)
    {
        $response = Http::get($issuesUrl);

        if ($response->successful()) {
            $issuesAndPullRequests = $response->json();
            $issues = array_filter($issuesAndPullRequests, function ($item) {
                return !isset($item['pull_request']);
            });
            return $issues;
        } else {
            logger('[ERROR] Failed to fetch GitHub issues', ['response' => $response->body()]);
            return [];
        }
    }
}
