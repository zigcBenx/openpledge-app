<?php

namespace App\Actions\Issue;

use App\Models\Issue;

class GetUsersFinishedIssues
{
    public static function get($githubId)
    {
        return Issue::with([
            'repository.githubInstallation',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
        ])
            ->where('resolver_github_id', $githubId)
            ->withSum('donations', 'amount')
            ->take(5)
            ->get()
            ->each(function ($issue) {
                $issue->favorite = $issue->userFavorite->isNotEmpty();
            });
    }

    public static function getPaginated($githubId, $page)
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
            ->where('resolver_github_id', $githubId)
            ->withSum('donations', 'amount');

        $issues = $query->paginate($perPage, $columns, $pageName, $page);

        $issues->getCollection()->transform(function ($issue) {
            $issue->favorite = $issue->userFavorite->isNotEmpty();
            return $issue;
        });

        return $issues;
    }
}