<?php

namespace App\Actions\User;

use App\Actions\Company\GetOrCreateCompany;
use App\Models\ProgrammingLanguageable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QuizSubmissions
{
    public static function handleNewUserQuizSubmission($newUserQuizSubmission)
    {
        $user = Auth::user();

        if (!empty($newUserQuizSubmission['companyName'])) {
            $user->company_id = GetOrCreateCompany::getId($newUserQuizSubmission['companyName']);
        }

        $user->job_title = $newUserQuizSubmission['jobTitle'];
        $user->is_contributor = false;
        $user->is_pledger = false;

        switch ($newUserQuizSubmission['intent']) {
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

        foreach ($newUserQuizSubmission['programmingLanguages'] as $programmingLanguageId) {
            ProgrammingLanguageable::create([
                'programming_language_id' => $programmingLanguageId,
                'programming_languageable_id' => $user->id,
                'programming_languageable_type' => User::class,
            ]);
        }

        return response()->json([
            'message' => "Time to Shine, Rockstar! You're ready to leave your mark on open source!"
        ]);
    }
}
