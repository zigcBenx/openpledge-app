<?php

namespace App\Actions\Payment;

use App\Models\Donation;
use Stripe\Stripe;
use Stripe\Transfer;

class TransferFunds
{
    public static function transfer(string $destinationStripeId, $amount)
    {
        if ($amount <= 0) {
            return;
        }

        Stripe::setApiKey(config('app.stripe_secret'));

        try {
            $transfer = Transfer::create([
                'amount' => $amount * 100, // Multiplied by 100 because Stripe expects the amount in cents
                'currency' => 'eur',
                'destination' => $destinationStripeId
            ]);

            return $transfer->id;
        } catch (\Exception $e) {
            logger('[ERROR] Stripe Transfer failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}