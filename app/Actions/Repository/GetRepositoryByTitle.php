<?php

namespace App\Actions\Repository;

use App\Models\Repository;

class GetRepositoryByTitle
{
    public static function get($title)
    {
        return Repository::with(['issues' => function ($query) {
            $query->withSum('donations', 'amount');
        }])->where('title', $title)->first();
    }
}
