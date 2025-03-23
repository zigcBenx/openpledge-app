<?php

namespace App\Services;

class PayoutFeeService
{
    /**
     * Fee to cover Stripe Connect payout pricing,
     * https://stripe.com/en-si/connect/pricing
     * @param float $payoutAmount
     * @return float
     */
    public static function calculate(float $payoutAmount): float
    {
        // TODO: 2€ is price per month, so you need to check
        // if this user has already made payout this month.
        // In that case you don't subtract 2€.
        return $payoutAmount - 2 - 0.1 - ($payoutAmount * 0.0025);
    }

    public static function hasEnoughFunds($payoutAmount): bool
    {
        return self::calculate($payoutAmount) > 0.0;
    }
}
