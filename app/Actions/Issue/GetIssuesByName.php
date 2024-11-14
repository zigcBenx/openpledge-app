<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use App\Services\GithubService;
use App\Models\GitHubInstallation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GetIssuesByName
{
    public static function get($githubUser, $repositoryName, $repositoryGithubInstallationId)
    {
        $pledgedIssues = Issue::query()
            ->where('github_url', 'LIKE', "https://github.com/$githubUser/$repositoryName/issues%")
            ->leftJoin('donations', 'donations.donatable_id', '=', 'issues.id')
            ->select('issues.*', DB::raw('SUM(donations.amount) as donations_sum_amount'))
            ->groupBy('issues.id')
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

        $issues = self::getIssuesFromGitHub($repositoryData["issues_url"]);

        $mergedIssues = [];

        foreach ($pledgedIssues as $pledgedIssue) {
            $mergedIssues[$pledgedIssue['github_id']] = $pledgedIssue;
        }

        foreach ($issues as $issue) {
            if (!isset($mergedIssues[$issue['id']])) {
                $mergedIssues[$issue['id']] = [
                    'id' => $issue['id'],
                    'title' => $issue['title'],
                    'github_url' => $issue['html_url'],
                    'state' => $issue['state'],
                    'labels' => $issue['labels'],
                    'user_avatar' => $issue['user']['avatar_url'],
                    'github_created_at' => $issue['created_at'],
                    'isExternal' => true
                ];
            }
        }

        return array_values($mergedIssues);
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
