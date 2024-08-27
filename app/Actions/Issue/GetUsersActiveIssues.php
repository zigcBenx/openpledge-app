<?php

namespace App\Actions\Issue;

use App\Models\Issue;

class GetUsersActiveIssues
{
    public static function get($userId)
    {
        return Issue::with([
            'repository.githubInstallation',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
        ])
            ->whereHas('resolvers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('state', 'open')
            ->withSum('donations', 'amount')
            ->take(5)
            ->get()
            ->each(function ($issue) {
                $issue->favorite = $issue->userFavorite->isNotEmpty();
            });
    }
}