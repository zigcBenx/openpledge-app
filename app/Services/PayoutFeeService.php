<?php

namespace App\Services;

class PayoutFeeService
{
    /**
     * Fee to cover Stripe Connect payout pricing,
     * https://stripe.com/en-si/connect/pricing
     */

    /**
     * We subtract 2$ because we allow only one payout per month.
     * Therefore, we need to pay stripe commission for
     * active user that month that payout occurred.
     * @param float $payoutAmount
     * @param bool $subtractMonthlyFee
     * @return float
     */
    public static function calculate(float $payoutAmount, bool $subtractMonthlyFee = false): float
    {
        return $payoutAmount - ($subtractMonthlyFee ? 2 : 0) - 0.1 - ($payoutAmount * 0.0025);
    }
}
