<?php

namespace App\Actions\Favorite;

use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class StoreFavorite
{
    public static function handle($favorableId, $favorableType)
    {
        $user = Auth::user();

        $modelClass = "App\\Models\\$favorableType";
        $favorableModel = class_exists($modelClass) ? $modelClass : null;

        if (!$favorableModel) {
            return response()->json(['error' => 'Invalid favorable type'], 400);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('favorable_id', $favorableId)
            ->where('favorable_type', $favorableModel)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => "$favorableType removed from Favorites"]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'favorable_id' => $favorableId,
            'favorable_type' => $favorableModel,
        ]);

        return response()->json(['message' => "$favorableType added to Favorites. View all >"]);
    }
}
