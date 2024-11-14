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
                'holding_days' => env('PAYMENT_HOLDING_DAYS'),
                'support_email' => env('FEEDBACK_MAIL')
            ])
            ->subject("Action Required: Connect Your Stripe Account to Receive Your $this->type");
    }
}