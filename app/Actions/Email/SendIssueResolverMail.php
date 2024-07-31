<?php

namespace App\Actions\Email;

use App\Mail\IssueResolverMail;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendIssueResolverMail
{
    public static function send($resolverMail, $resolverName, $issueId)
    {
        try {
            Mail::to($resolverMail)->send(new IssueResolverMail($resolverName, $issueId));
        } catch (Exception $e) {
            Log::error('Error sending Issue Resolver Mail: ' . $e->getMessage(), [
                'resolverMail' => $resolverMail,
                'resolverName' => $resolverName,
                'issueId' => $issueId,
            ]);
        }
    }
}
