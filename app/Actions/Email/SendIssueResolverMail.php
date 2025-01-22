<?php

namespace App\Actions\Email;

use App\Mail\IssueResolverMail;
use App\Mail\IssueNonResolversMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendIssueResolverMail
{
    public static function send($resolverMail, $resolverName, $issueId, $usersWithActiveIssue)
    {
        try {
            // Send email to the resolver
            Mail::to($resolverMail)->send(new IssueResolverMail($resolverName, $issueId));

            // Send emails to users with this issue as an active issue but is not the resolver
            foreach ($usersWithActiveIssue as $user) {
                Mail::to($user->email)->send(new IssueNonResolversMail($user->name, $issueId));
            }
        } catch (Exception $e) {
            logger('[ERROR] Error sending Issue Resolver Mail: ' . $e->getMessage(), [
                'resolverMail' => $resolverMail,
                'resolverName' => $resolverName,
                'issueId' => $issueId,
                'stack_trace' => $e->getTraceAsString()
            ]);
        }
    }
}