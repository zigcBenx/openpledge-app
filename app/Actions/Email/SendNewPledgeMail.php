<?php

namespace App\Actions\Email;

use App\Mail\NewPledgeMail;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewPledgeMail
{
    public static function send($donorMail, $donorName, $issueId)
    {
        try {
            Mail::to($donorMail)->send(new NewPledgeMail($donorName, $issueId));
        } catch (Exception $e) {
            Log::error('Error sending New Pledge Mail: ' . $e->getMessage(), [
                'donorMail' => $donorMail,
                'donorName' => $donorName,
                'issueId' => $issueId,
            ]);
        }
    }
}
