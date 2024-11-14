<?php

namespace App\Actions\Email;

use App\Mail\ConnectStripeMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendConnectStripeMail
{
    public static function send($email, $name, $amount, $type)
    {
        try {
            if (!in_array($type, ['Refund', 'Payout'])) {
                throw new \InvalidArgumentException('Type must be either "Refund" or "Payout".');
            }

            Mail::to($email)->send(new ConnectStripeMail($name, $amount, $type));
        } catch (Exception $e) {
            logger('[ERROR] Error sending Connect Stripe Mail: ' . $e->getMessage(), [
                'email' => $email,
                'name' => $name,
                'amount' => $amount,
                'type' => $type,
            ]);
        }
    }

}