<?php

namespace App\Actions\Repository;

use App\Models\Repository;

class GetRepositoryByTitle
{
    public static function get($title)
    {
        $repository = Repository::with(['programmingLanguages:id,name', 'userFavorite' ,'issues' => function ($query) {
            $query->with('repository.programmingLanguages:id,name', 'userFavorite', 'resolvedBy')
                ->withSum('donations', 'amount')
                ->whereHas('donations', function ($query) {
                    $query->where('amount', '>', 0);
                });;
        }])->withCount(['issues' => function ($query) {
            $query->whereHas('donations', function ($query) {
                $query->where('amount', '>', 0);
            });
        }])
        ->where('title', $title)->first();

        if ($repository) {
            $repository->issues->each(function ($issue) {
                $issue->favorite = $issue->userFavorite->isNotEmpty();
            });

            $repository->favorite = $repository->userFavorite->isNotEmpty();
        }

        return $repository;
    }
}
