<?php

namespace App\Actions\Payment;

use Stripe\Stripe;
use Stripe\Transfer;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\ApiErrorException;

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
                'currency' => config('app.stripe_currency'),
                'destination' => $destinationStripeId,
            ];

            if ($chargeId !== null) {
                $transferOptions['source_transaction'] = $chargeId;
            }

            $transfer = Transfer::create($transferOptions);

            return $transfer->id;
        } catch (ApiErrorException $e) {
            logger('[ERROR] Stripe Transfer failed', [
                'error' => $e->getMessage(),
                'stripe_error' => $e->getError(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Get the user-friendly error message from Stripe
            $errorMessage = $e->getError()->message ?? $e->getMessage();

            // Throw a new exception with just the error message
            throw new \Exception($errorMessage);
        } catch (\Exception $e) {
            logger('[ERROR] Stripe Transfer failed', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
