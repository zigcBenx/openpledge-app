<?php

namespace App\Actions\Profile;

use Illuminate\Support\Facades\Auth;

class UpdateProfile
{
    public static function toggleAnonymousPledging($isPledgingAnonymously)
    {
        $user = Auth::user();

        $user->is_pledging_anonymously = $isPledgingAnonymously;
        $user->save();

        return response()->json([
            'message' => $isPledgingAnonymously
                ? "Incognito mode activated! Your pledges will be anonymous"
                : "Let the world see your impact! Your pledges will be public"
        ]);
    }
}
