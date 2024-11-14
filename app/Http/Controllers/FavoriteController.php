<?php

namespace App\Http\Controllers;

use App\Actions\Favorite\StoreFavorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        return StoreFavorite::handle(
            $request->favorable_id,
            $request->favorable_type
        );
    }
}
