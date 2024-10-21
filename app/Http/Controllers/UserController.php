<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\User\QuizSubmissions;

class UserController extends Controller
{
    public function handleNewUserQuizSubmission(Request $request)
    {
        $newUserQuizSubmission = $request->input('newUserQuizSubmission');
        return QuizSubmissions::handleNewUserQuizSubmission($newUserQuizSubmission);
    }
}
