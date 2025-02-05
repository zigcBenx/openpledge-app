<?php

namespace App\Actions\Email;

use App\Mail\NotifyPledgerMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendNotifyPledgersMail
{
    public static function send($pledgers, $issueId)
    {
        try {
            foreach ($pledgers as $pledger) {
                Mail::to($pledger['email'])->send(new NotifyPledgerMail($pledger['name'], $issueId));
            }
        } catch (Exception $e) {
            logger('[ERROR] Error sending Notify Pledgers Mail: ' . $e->getMessage(), [
                'pledgers' => $pledgers,
                'issueId' => $issueId,
                'stack_trace' => $e->getTraceAsString()
            ]);
        }
    }
}