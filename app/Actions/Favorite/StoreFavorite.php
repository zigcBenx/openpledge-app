<?php

namespace App\Actions\Favorite;

use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class StoreFavorite
{
    public static function handle($favorableId, $favorableType)
    {
        $user = Auth::user();
        $favorableModel = self::getFavorableModel($favorableType);

        if (!$favorableModel) {
            return response()->json(['error' => 'Invalid favorable type'], 400);
        }

        $favorite = self::findFavorite($user->id, $favorableId, $favorableModel);

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => "$favorableType removed from Favorites"]);
        }

        self::createFavorite($user->id, $favorableId, $favorableModel);

        return response()->json(['message' => "$favorableType added to Favorites"]);
    }

    private static function getFavorableModel($favorableType)
    {
        $modelClass = "App\\Models\\$favorableType";
        return class_exists($modelClass) ? $modelClass : null;
    }

    private static function findFavorite($userId, $favorableId, $favorableModel)
    {
        return Favorite::where('user_id', $userId)
            ->where('favorable_id', $favorableId)
            ->where('favorable_type', $favorableModel)
            ->first();
    }

    private static function createFavorite($userId, $favorableId, $favorableModel)
    {
        Favorite::create([
            'user_id' => $userId,
            'favorable_id' => $favorableId,
            'favorable_type' => $favorableModel,
        ]);
    }
}
