<?php

namespace App\Actions\Repository;

use App\Models\Repository;

class GetRepositoryByTitle
{
    public static function get($title)
    {
        return Repository::with(['programmingLanguages:id,name','issues' => function ($query) {
            $query->with('repository.programmingLanguages:id,name')
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
    }
}
