<?php

namespace App\Actions\Favorite;

use App\Actions\Repository\GetDetailedRepositoryData;
use Illuminate\Support\Facades\Auth;
use App\Models\Repository;

class GetFavoriteRepositories
{
    public static function get()
    {
        $userId = Auth::id();
        $favoriteRepositoryIds = GetFavorites::getFavoriteIdsByUserAndType($userId, Repository::class);

        return Repository::with([
            'programmingLanguages:id,name',
            'userFavorite',
            'githubInstallation',
        ])
            ->withSum('donations', 'amount')
            ->withCount(['issues as pledged_issues_count' => function ($query) {
                $query->whereHas('donations');
            }])
            ->whereIn('id', $favoriteRepositoryIds)
            ->take(2)
            ->get()
            ->each(function ($repository) {
                GetDetailedRepositoryData::get($repository);
            });
    }

    public static function getPaginated($page)
    {
        $perPage = 6;
        $columns = ['*'];
        $pageName = 'page';

        $userId = Auth::id();
        $favoriteRepositoryIds = GetFavorites::getFavoriteIdsByUserAndType($userId, Repository::class);

        $query = Repository::with([
            'programmingLanguages:id,name',
            'userFavorite',
            'githubInstallation',
        ])
            ->withSum('donations', 'amount')
            ->withCount(['issues as pledged_issues_count' => function ($query) {
                $query->whereHas('donations');
            }])
            ->whereIn('id', $favoriteRepositoryIds);

        $repositories = $query->paginate($perPage, $columns, $pageName, $page);

        $repositories->getCollection()->transform(function ($repository) {
            GetDetailedRepositoryData::get($repository);
            return $repository;
        });

        return $repositories;
    }
}
