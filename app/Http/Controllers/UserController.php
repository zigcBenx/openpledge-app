<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\User\QuizSubmissions;

class UserController extends Controller
{
    public function handleUserIntentQuiz(Request $request)
    {
        $userIntentQuizSubmission = $request->input('userIntentQuizSubmission');
        return QuizSubmissions::handleUserIntentQuiz($userIntentQuizSubmission);
    }
}
