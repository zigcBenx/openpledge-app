<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use Illuminate\Support\Facades\Validator;

class CreateNewDonation
{
    public static function create(array $input): Donation
    {
        Validator::make($input, [
            'donatable_id'   => ['required', 'numeric'],
            'donatable_type' => ['required', 'string'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'donor_id'       => ['required', 'numeric', 'exists:users,id'],
        ])->validate();

        return Donation::create($input);
    }
}
