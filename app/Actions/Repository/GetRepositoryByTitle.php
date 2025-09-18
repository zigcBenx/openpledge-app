<?php

namespace App\Actions\Repository;

use App\Models\Repository;

class GetRepositoryByTitle
{
    public static function get($title)
    {
        $repository = Repository::with(['programmingLanguages:id,name', 'userFavorite', 'issues' => function ($query) {
            $query->with('repository.settings', 'repository.programmingLanguages:id,name', 'userFavorite', 'resolvedBy', 'labels')
                ->withSum('donations', 'net_amount')
                ->whereHas('donations', function ($query) {
                    $query->where('net_amount', '>', 0);
                })
                ->orderByRaw("CASE WHEN state = 'open' THEN 0 ELSE 1 END")
                ->orderByDesc('donations_sum_net_amount');
        }])->withCount(['issues' => function ($query) {
            $query->whereHas('donations', function ($query) {
                $query->where('net_amount', '>', 0);
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
