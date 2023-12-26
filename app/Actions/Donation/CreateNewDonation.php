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

        return Donation::create([
            'donatable_id'   => $input['donatable_id'],
            'donatable_type' => $input['donatable_type'],
            'amount'         => $input['amount'],
            'transaction_id' => $input['transaction_id'],
            'donor_id'       => $input['donor_id'],
        ]);
    }
}
