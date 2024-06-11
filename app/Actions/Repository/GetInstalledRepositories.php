<?php

namespace App\Actions\Repository;

use App\Models\Repository;
use Illuminate\Support\Facades\Auth;

/**
 * Retrieves the repositories associated with an authenticated user Id.
 * This action fetches repositories that were installed via the GitHub App.
 * Additionally, it loads the related issues and calculates the total donation amount for each issue.
 */
class GetInstalledRepositories
{
    public static function get()
    {
        $user = Auth::user();
        return Repository::with(['issues' => function ($query) {
            $query->withSum('donations', 'amount');
        }])->where('user_id', $user->id)->get();
    }
}
