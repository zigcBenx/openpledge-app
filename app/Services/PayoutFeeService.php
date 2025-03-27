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

    /**
     * We subtract 2€ because we allow only one payout per month.
     * Therefore, we need to pay stripe commission for
     * active user that month that payout occurred.
     * @param float $payoutAmount
     * @return float
     */
    public static function calculate(float $payoutAmount): float
    {
        return $payoutAmount - 2 - 0.1 - ($payoutAmount * 0.0025);
    }

    public static function hasEnoughFunds($payoutAmount): bool
    {
        return self::calculate($payoutAmount) > 0.0;
    }
}
