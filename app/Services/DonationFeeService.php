<?php

namespace App\Services;

class DonationFeeService
{

    public static function calculateAmounts(float $grossAmount): array
    {
        $feePercentage = config('app.platform_fee_percentage');
        $feeAmount = ($grossAmount * $feePercentage) / 100;
        $netAmount = round($grossAmount - $feeAmount, 2);

        return [
            'gross_amount' => $grossAmount,
            'net_amount' => $netAmount,
            'fee_percentage' => $feePercentage
        ];
    }
}
