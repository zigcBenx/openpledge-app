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

    public static function getPaginated($userId, $page)
    {
        $perPage = 6;
        $columns = ['*'];
        $pageName = 'page';

        $query = Issue::with([
            'repository.githubInstallation',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
        ])
            ->whereHas('resolvers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('state', 'open')
            ->withSum('donations', 'amount');

        $issues = $query->paginate($perPage, $columns, $pageName, $page);

        $issues->getCollection()->transform(function ($issue) {
            $issue->favorite = $issue->userFavorite->isNotEmpty();
            return $issue;
        });

        return $issues;
    }
}