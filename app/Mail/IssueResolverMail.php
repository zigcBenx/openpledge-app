<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueResolverMail extends Mailable
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

        return $this->view('emails.issue-resolver')
            ->with([
                'resolverName' => $this->resolverName,
                'issueLink' => $issueLink,
            ])
            ->subject('Congratulations on Resolving an Issue!');
    }
}