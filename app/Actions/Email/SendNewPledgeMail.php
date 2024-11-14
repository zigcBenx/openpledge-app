<?php

namespace App\Actions\Email;

use App\Mail\NewPledgeMail;
use App\Mail\NewPledgeForResolversMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendNewPledgeMail
{
    public static function send($donorMail, $donorName, $issueId, $amount, $usersWithActiveIssue)
    {
        try {
            // Send email to thank the donor
            Mail::to($donorMail)->send(new NewPledgeMail($donorName, $issueId));

            // Send emails to users with this issue as an active issue
            foreach ($usersWithActiveIssue as $user) {
                Mail::to($user->email)->send(new NewPledgeForResolversMail($user->name, $issueId, $amount));
            }
        } catch (Exception $e) {
            logger('[ERROR] Error sending New Pledge Mail: ' . $e->getMessage(), [
                'donorMail' => $donorMail,
                'donorName' => $donorName,
                'issueId' => $issueId,
            ]);
        }
    }
}