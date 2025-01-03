<?php

namespace App\Actions\Email;

use App\Mail\RefundMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendRefundMail
{
    public static function send($email, $name, $amount)
    {
        try {
            Mail::to($email)->send(new RefundMail($name, $amount));
        } catch (Exception $e) {
            logger('[ERROR] Error sending Refund Mail: ' . $e->getMessage(), [
                'email' => $email,
                'name' => $name,
                'amount' => $amount,
                'stack_trace' => $e->getTraceAsString()
            ]);
        }
    }

}