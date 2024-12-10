<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundMail extends Mailable
{
    use Queueable, SerializesModels;
    private $name;
    private $amount;

    public function __construct($name, $amount)
    {
        $this->name = $name;
        $this->amount = $amount;
    }

    public function build()
    {
        return $this->view('emails.refund')
            ->with([
                'name' => $this->name,
                'amount' => $this->amount,
                'support_email' => config('mail.feedback_mail')
            ])
            ->subject("Your Refund Has Been Issued");
    }
}