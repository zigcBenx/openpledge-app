<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueNonResolversMail extends Mailable
{
    use Queueable, SerializesModels;
    private $resolverName;
    private $issueId;

    public function __construct($resolverName, $issueId)
    {
        $this->resolverName = $resolverName;
        $this->issueId = $issueId;
    }

    public function build()
    {
        $appUrl = env('APP_URL');
        $issueLink = "{$appUrl}/issues/{$this->issueId}";
        $discoverLink = "{$appUrl}/discover/issues";

        return $this->view('emails.issue-non-resolvers')
            ->with([
                'resolverName' => $this->resolverName,
                'issueLink' => $issueLink,
                'discoverLink' => $discoverLink
            ])
            ->subject('An Issue You Were Working On Has Been Resolved');
    }
}