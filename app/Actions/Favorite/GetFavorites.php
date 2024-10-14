<?php

namespace App\Actions\Favorite;

use App\Models\Favorite;

class GetFavorites
{
    public static function get()
    {
        return [
            'issues' => GetFavoriteIssues::get(),
            'repositories' => GetFavoriteRepositories::get(),
        ];
    }

    public static function getPaginated($page, $showIssues)
    {
        if ($showIssues) {
            return GetFavoriteIssues::getPaginated($page);
        } else {
            return GetFavoriteRepositories::getPaginated($page);
        }
    }

    public static function getFavoriteIdsByUserAndType($userId, $model)
    {
        return Favorite::where('user_id', $userId)
            ->where('favorable_type', $model)
            ->pluck('favorable_id');
    }
}
