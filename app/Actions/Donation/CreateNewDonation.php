<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use Illuminate\Support\Facades\Validator;

class CreateNewDonation
{
    public static function create(array $input): Donation
    {
        return Donation::create($input);
    }
}
