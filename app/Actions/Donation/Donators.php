<?php

namespace App\Actions\Donation;

use App\Models\Donation;

class Donators
{
    public static function getTopDonors()
    {
        return Donation::select('donor_id')
            ->selectRaw('SUM(gross_amount) as total_donated')
            ->groupBy('donor_id')
            ->whereNotNull('donor_id')
            ->orderByDesc('total_donated')
            ->with('user')
            ->take(5)
            ->get();
    }
}
