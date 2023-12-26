<?php

namespace App\Actions\Donation;

use App\Models\Donation;

class GetDonations
{
    public static function get()
    {
        return Donation::all();
    }
}
