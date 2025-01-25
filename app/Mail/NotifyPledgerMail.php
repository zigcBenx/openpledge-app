<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyPledgerMail extends Mailable
{
    use Queueable, SerializesModels;

    private $pledgerName;
    private $issueId;

    public function __construct($pledgerName, $issueId)
    {
        $this->pledgerName = $pledgerName;
        $this->issueId = $issueId;
    }

    public function build()
    {
        $appUrl = config('app.url');
        $issueLink = "{$appUrl}/issues/{$this->issueId}";
        
        return $this->view('emails.notify-pledger')
            ->with([
                'pledgerName' => $this->pledgerName,
                'issueLink' => $issueLink,
            ])
            ->subject('Good news! Your pledged issue is now resolved 🎉');
    }
}
