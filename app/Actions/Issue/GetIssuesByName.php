<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use App\Actions\Github\HandleGithubAppCallback;
use App\Models\GitHubInstallation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        if(!isset($repositoryGithubInstallationId)) {
            return $pledgedIssues;
        } 
        
        $installation = GitHubInstallation::where('installation_id', $repositoryGithubInstallationId)->first();
        
        $installedRepositories = HandleGithubAppCallback::fetchGithubRepositories($installation->access_token, $installation->installation_id);

        $repositoryData = array_filter($installedRepositories, function($installedRepository) use ($githubUser, $repositoryName) {
            return $installedRepository['full_name'] == $githubUser . '/' . $repositoryName;
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
                    'created_at' => $issue['created_at']
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
            $issues = [];
            foreach ($issuesAndPullRequests as $issueOrPullRequest) {
                if (isset($issueOrPullRequest['pull_request'])) {
                    $issues[] = $issueOrPullRequest;
                }
            }

            return $issues;
        } else {
            Log::error('Failed to fetch GitHub issues', ['response' => $response->body()]);
            return [];
        }
    }
}
