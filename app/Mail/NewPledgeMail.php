<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPledgeMail extends Mailable
{
    use Queueable, SerializesModels;

    private $donorName;
    private $issueId;

    public function __construct($donorName, $issueId)
    {
        $this->donorName = $donorName;
        $this->issueId = $issueId;
    }

    public function build()
    {
        $appUrl = config('app.url');
        $issueLink = "{$appUrl}/issues/{$this->issueId}";
        
        return $this->view('emails.new-pledge')
            ->with([
                'donorName' => $this->donorName,
                'issueLink' => $issueLink,
            ])
            ->subject('Thank You for Your Pledge!');
    }
}