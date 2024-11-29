<?php

namespace App\Actions\Email;

use App\Mail\FeedbackMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendFeedbackMail
{
    public static function send($email, $content)
    {
        try {
            Mail::to(config('mail.feedback_mail'))->send(new FeedbackMail($email, $content));
        } catch (Exception $e) {
            logger('[ERROR] Error sending Feedback Mail: ' . $e->getMessage(), [
                'email' => $email,
                'content' => $content
            ]);
        }
    }
}