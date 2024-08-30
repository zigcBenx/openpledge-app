<?php

namespace App\Actions\Favorite;

use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\Repository;
use App\Models\Issue;
use App\Actions\Issue\GetIssuesByName;

class GetFavorites
{
    public static function get()
    {
        return [
            'issues' => self::getIssues(),
            'repositories' => self::getRepositories(),
        ];
    }

    public static function getPaginated($page, $showIssues)
    {
        if ($showIssues) {
            return self::getPaginatedIssues($page);
        } else {
            return self::getPaginatedRepositories($page);
        }
    }

    protected static function getIssues()
    {
        $userId = Auth::id();
        $favoriteIssueIds = self::getFavoriteIds($userId, Issue::class);

        return Issue::with([
            'repository.githubInstallation',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
        ])
            ->withSum('donations', 'amount')
            ->whereIn('id', $favoriteIssueIds)
            ->take(3)
            ->get()
            ->each(function ($issue) {
                $issue->favorite = $issue->userFavorite->isNotEmpty();
            });
    }

    protected static function getPaginatedIssues($page)
    {
        $perPage = 6;
        $columns = ['*'];
        $pageName = 'page';

        $userId = Auth::id();
        $favoriteIssueIds = self::getFavoriteIds($userId, Issue::class);

        $query = Issue::with([
            'repository.githubInstallation',
            'donations.user',
            'userFavorite',
            'programmingLanguages:id,name',
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


    protected static function getRepositories()
    {
        $userId = Auth::id();
        $favoriteRepositoryIds = self::getFavoriteIds($userId, Repository::class);

        return Repository::with([
            'programmingLanguages:id,name',
            'userFavorite',
            'githubInstallation',
        ])
            ->withSum('donations', 'amount')
            ->withCount([
                'issues as pledged_issues_count' => function ($query) {
                    $query->where('state', 'open');
                }
            ])
            ->whereIn('id', $favoriteRepositoryIds)
            ->take(2)
            ->get()
            ->each(function ($repository) {
                self::processRepository($repository);
            });
    }

    protected static function getPaginatedRepositories($page)
    {
        $perPage = 6;
        $columns = ['*'];
        $pageName = 'page';

        $userId = Auth::id();
        $favoriteRepositoryIds = self::getFavoriteIds($userId, Repository::class);

        $query = Repository::with([
            'programmingLanguages:id,name',
            'userFavorite',
            'githubInstallation',
        ])
            ->withSum('donations', 'amount')
            ->withCount([
                'issues as pledged_issues_count' => function ($query) {
                    $query->where('state', 'open');
                }
            ])
            ->whereIn('id', $favoriteRepositoryIds);

        $repositories = $query->paginate($perPage, $columns, $pageName, $page);

        $repositories->getCollection()->transform(function ($repository) {
            self::processRepository($repository);
            return $repository;
        });

        return $repositories;
    }

    protected static function getFavoriteIds($userId, $model)
    {
        return Favorite::where('user_id', $userId)
            ->where('favorable_type', $model)
            ->pluck('favorable_id');
    }

    protected static function processRepository($repository): void
    {
        [$githubUser, $repositoryName] = explode('/', $repository->title);
        $issues = GetIssuesByName::get($githubUser, $repositoryName, $repository->github_installation_id);

        $repository->favorite = $repository->userFavorite->isNotEmpty();
        $repository->open_issues_count = count($issues);
        $repository->issues_donations_sum_amount = array_reduce($issues, function ($sum, $issue) {
            return $sum + ($issue->donations_sum_amount ?? 0);
        }, 0);
    }
}
