<?php

namespace App\Actions\Repository;

use App\Models\Issue;
use App\Models\Repository;

class GetRepositories
{
    public static function get()
    {
        return Repository::with('issues')
        ->addSelect(['donations_sum' => Issue::selectRaw('sum(donations.net_amount) as sum')
            ->join('donations', function ($join) {
                $join->on('donations.donatable_id', '=', 'issues.id')
                     ->where('donations.donatable_type', Issue::class);
            })
            ->whereColumn('issues.repository_id', 'repositories.id')])
        ->get();
    }
}
