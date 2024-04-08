<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    private $content;
    private $email;

    public function __construct($content, $email)
    {
        $this->content = $content;
        $this->email = $email;
    }

    public function build()
    {
        return $this->view('emails.campaign-default')->with(['mail_content' => $this->content, 'email' => $this->email]);
    }
}
