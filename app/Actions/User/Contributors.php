<?php

namespace App\Actions\User;

use App\Models\Issue;
use App\Services\GithubService;

class Contributors
{
    public static function getTopContributors()
    {
        $topResolvers = Issue::where('state', 'closed')
            ->whereNotNull('resolver_github_id')
            ->selectRaw('resolver_github_id, COUNT(*) as issue_count')
            ->groupBy('resolver_github_id')
            ->orderByDesc('issue_count')
            ->limit(5)
            ->get();

        $githubUsers = [];
        foreach ($topResolvers as $resolver) {
            $githubUser = GithubService::getUserByGithubId($resolver->resolver_github_id);
            $githubUser['issueCount'] = $resolver->issue_count;
            $githubUsers[] = $githubUser;
        }

        return $githubUsers;
    }
}