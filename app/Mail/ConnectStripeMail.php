<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConnectStripeMail extends Mailable
{
    use Queueable, SerializesModels;
    private $name;
    private $amount;
    private $type;

    public function __construct($name, $amount, $type)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->type = $type;
    }

    public function build()
    {
        return $this->view('emails.connect-stripe')
            ->with([
                'name' => $this->name,
                'amount' => $this->amount,
                'type' => $this->type,
                'holding_days' => config('app.payment_holding_days'),
                'support_email' => config('mail.feedback_mail')
            ])
            ->subject("Action Required: Connect Your Stripe Account to Receive Your $this->type");
    }
}