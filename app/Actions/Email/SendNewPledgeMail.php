<?php

namespace App\Actions\Email;

use App\Mail\NewPledgeMail;
use App\Mail\NewPledgeForResolversMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewPledgeMail
{
    public static function send($donorMail, $donorName, $issueId, $amount)
    {
        try {
            // Send email to thank the donor
            Mail::to($donorMail)->send(new NewPledgeMail($donorName, $issueId));

            // Query users who have this issue as an active issue (resolvers)
            $usersWithActiveIssue = User::whereHas('active_issues', function ($query) use ($issueId) {
                $query->where('issue_id', $issueId);
            })->get();

            // Send emails to users with this issue as an active issue
            foreach ($usersWithActiveIssue as $user) {
                Mail::to($user->email)->send(new NewPledgeForResolversMail($user->name, $issueId, $amount));
            }
        } catch (Exception $e) {
            Log::error('Error sending New Pledge Mail: ' . $e->getMessage(), [
                'donorMail' => $donorMail,
                'donorName' => $donorName,
                'issueId' => $issueId,
            ]);
        }
    }
}