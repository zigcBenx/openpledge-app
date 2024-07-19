<?php

namespace App\Actions\Issue;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetIssueActivity
{
    public static function get($issueGithubUrl, $githubAccessToken, $donations)
    {
        $issueDetails = self::parseIssueUrl($issueGithubUrl);

        if (!$issueDetails) {
            Log::warning("Invalid GitHub issue URL provided: {$issueGithubUrl}");
            return null;
        }

        return self::fetchIssueActivity($issueDetails, $githubAccessToken, $donations);
    }

    private static function parseIssueUrl($url)
    {
        if (preg_match('/^https:\/\/github\.com\/([\w\-]+)\/([\w\-]+)\/issues\/(\d+)$/', $url, $matches)) {
            return ['user' => $matches[1], 'repo' => $matches[2], 'issueNumber' => $matches[3]];
        }

        return null;
    }

    private static function fetchIssueActivity($issueDetails, $accessToken, $donations)
    {
        $url = "https://api.github.com/repos/{$issueDetails['user']}/{$issueDetails['repo']}/issues/{$issueDetails['issueNumber']}/timeline";
        $response = Http::withToken($accessToken)->get($url);

        if ($response->successful()) {
            $activities = $response->json();

            if (isset($donations)) {
                $activities = array_merge($activities, $donations->toArray());
            }

            // Get the latest activities first
            usort($activities, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return $activities;
        } else {
            Log::error("Failed to fetch issue activity from GitHub: " . $response->body());
            return null;
        }
    }
}
