<?php

namespace App\Actions\User;

use Illuminate\Support\Facades\Auth;

class QuizSubmissions
{
    public static function handleUserIntentQuiz($userIntentQuizSubmission)
    {
        $user = Auth::user();

        $user->is_contributor = false;
        $user->is_pledger = false;

        switch ($userIntentQuizSubmission) {
            case 'userIsContributor':
                $user->is_contributor = true;
                break;
            case 'userIsPledger':
                $user->is_pledger = true;
                break;
            case 'userIsBoth':
                $user->is_contributor = true;
                $user->is_pledger = true;
                break;
        }

        $user->save();

        return response()->json([
            'message' => "Time to Shine, Rockstar! You're ready to leave your mark on open source!"
        ]);
    }
}
