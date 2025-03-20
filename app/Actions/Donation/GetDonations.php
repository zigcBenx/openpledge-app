<?php

namespace App\Actions\Donation;

use App\Models\Donation;

class GetDonations
{
    public static function get()
    {
        return Donation::all();
    }

    public static function getAnonymous()
    {
        return Donation::whereNull('donor_id')
            ->sum('gross_amount');
    }
}
