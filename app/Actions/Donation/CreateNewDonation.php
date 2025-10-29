<?php

namespace App\Actions\Donation;

use App\Models\Donation;

class CreateNewDonation
{
    public static function create(array $input): Donation
    {
        return Donation::create($input);
    }
}
