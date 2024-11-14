<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDonationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'donatable_id'   => ['required', 'numeric'],
            'donatable_type' => ['required', 'string'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'donor_id'       => ['required', 'numeric', 'exists:users,id']
        ];
    }
}
