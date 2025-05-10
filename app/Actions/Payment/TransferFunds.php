<?php

namespace App\Actions\Payment;

use Stripe\Stripe;
use Stripe\Transfer;

class TransferFunds
{
    public static function transfer(string $destinationStripeId, $amount, $chargeId)
    {
        if ($amount <= 0) {
            return;
        }

        Stripe::setApiKey(config('app.stripe_secret'));

        try {
            $transferOptions = [
                'amount' => (int)($amount * 100), // Multiplied by 100 because Stripe expects the amount in cents
                'currency' => 'eur',
                'destination' => $destinationStripeId,
            ];

            if ($chargeId !== null) {
                $transferOptions['source_transaction'] = $chargeId;
            }

            $transfer = Transfer::create($transferOptions);

            return $transfer->id;
        } catch (\Exception $e) {
            logger('[ERROR] Stripe Transfer failed', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
