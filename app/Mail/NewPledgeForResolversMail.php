<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPledgeForResolversMail extends Mailable
{
    use Queueable, SerializesModels;

    private $resolverName;
    private $issueId;
    private $amount;

    public function __construct($resolverName, $issueId, $amount)
    {
        $this->resolverName = $resolverName;
        $this->issueId = $issueId;
        $this->amount = $amount;
    }

    public function build()
    {
        $appUrl = env('APP_URL');
        $issueLink = "{$appUrl}/issues/{$this->issueId}";
        
        return $this->view('emails.new-pledge-for-resolvers')
            ->with([
                'resolverName' => $this->resolverName,
                'issueLink' => $issueLink,
                'amount' => $this->amount
            ])
            ->subject('New Pledge On Your Active Issue!');
    }
}