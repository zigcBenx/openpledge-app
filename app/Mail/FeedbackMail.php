<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    private $email;
    private $content;

    public function __construct($email, $content)
    {
        $this->email = $email;
        $this->content = $content;
    }

    public function build()
    {
        return $this->view('emails.feedback')->with(['email' => $this->email, 'content' => $this->content]);
    }
}
