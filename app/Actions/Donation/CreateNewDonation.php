<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use App\Services\DonationValidationService;

class CreateNewDonation
{
    public static function create(array $input): Donation
    {
        DonationValidationService::validateBeforeCreation($input);

        return Donation::create($input);
    }
}
