<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pledgeExpirationDate' => ['nullable', 'string', 'date_format:Y-m-d', 'after:today'],
            'paymentId'            => ['required', 'string', 'max:255'],
            'issue_id'             => ['required', 'integer', 'exists:issues,id'],
            'amount'               => ['required', 'numeric', 'min:0.01'],
            'email'                => ['nullable', 'email']
        ];
    }
}
