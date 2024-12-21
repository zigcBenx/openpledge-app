<?php

namespace App\Actions\Favorite;

use Illuminate\Support\Facades\Auth;
use App\Models\Issue;

class GetFavoriteIssues
{
    public static function get()
    {
        $userId = Auth::id();
        $favoriteIssueIds = GetFavorites::getFavoriteIdsByUserAndType($userId, Issue::class);

        return Issue::with([
            'repository.programmingLanguages',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
            'resolvedBy',
            'labels'
        ])
            ->withSum('donations', 'amount')
            ->whereIn('id', $favoriteIssueIds)
            ->take(3)
            ->get()
            ->each(function ($issue) {
                $issue->favorite = $issue->userFavorite->isNotEmpty();
            });
    }

    public static function getPaginated($page)
    {
        $perPage = 6;
        $columns = ['*'];
        $pageName = 'page';

        $userId = Auth::id();
        $favoriteIssueIds = GetFavorites::getFavoriteIdsByUserAndType($userId, Issue::class);

        $query = Issue::with([
            'repository.programmingLanguages',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
            'resolvedBy',
            'labels'
        ])
            ->withSum('donations', 'amount')
            ->whereIn('id', $favoriteIssueIds);

        $issues = $query->paginate($perPage, $columns, $pageName, $page);

        $issues->getCollection()->transform(function ($issue) {
            $issue->favorite = $issue->userFavorite->isNotEmpty();
            return $issue;
        });

        return $issues;
    }
}
