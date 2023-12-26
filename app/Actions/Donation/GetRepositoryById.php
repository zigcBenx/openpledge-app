<?php

namespace App\Actions\Donation;

use App\Models\Donation;

class GetDonationById
{
    public static function get($id)
    {
        return Donation::find($id);
    }
}
