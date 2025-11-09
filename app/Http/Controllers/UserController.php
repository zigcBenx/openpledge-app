<?php

namespace App\Http\Controllers;

use App\Actions\Email\SendFeedbackMail;
use App\Models\ProgrammingLanguage;
use Illuminate\Http\Request;
use App\Actions\User\QuizSubmissions;

class UserController extends Controller
{
    public function handleNewUserQuizSubmission(Request $request)
    {
        $newUserQuizSubmission = $request->input('newUserQuizSubmission');
        return QuizSubmissions::handleNewUserQuizSubmission($newUserQuizSubmission);
    }

    public function handleUserFeedbackSubmission(Request $request)
    {
        $email = $request->input('email');
        $content = $request->input('content');

        SendFeedbackMail::send($email, $content);

        return response()->json([
            'toastMessage' => "Thank you! Your feedback was submitted successfully.",
            'modalMessage' => "We sincerely appreciate your feedback! It helps us improve the platform. Our team will review it soon.",
        ]);
    }

    public function getProgrammingLanguages()
    {
        $languages = ProgrammingLanguage::orderBy('popular', 'desc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'popular']);

        return response()->json($languages);
    }
}
