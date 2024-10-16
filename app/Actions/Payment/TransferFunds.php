<?php

namespace App\Actions\Payment;

use App\Models\Donation;
use Stripe\Stripe;
use Stripe\Transfer;

class TransferFunds
{
    public static function transfer(string $destinationStripeId, $amount, int $issueId)
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

            logger('[INFO] Funds transferred', ['issue_id' => $issueId, 'amount' => $amount, 'stripe_transfer_id' => $transfer->id]);

            Donation::where('donatable_id', $issueId)->update(['paid' => true]);
        } catch (\Exception $e) {
            logger('[ERROR] Stripe Transfer failed', ['error' => $e->getMessage(), 'issue_id' => $issueId]);
            throw $e;
        }
    }
}