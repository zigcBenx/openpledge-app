<?php

namespace App\Http\Requests;

use App\Rules\FutureExpirationDate;
use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pledgeExpirationDate.value' => ['nullable', 'string'],
            'pledgeExpirationYear'       => ['nullable', 'digits:4', new FutureExpirationDate($this->input('pledgeExpirationDate.value'))],
            'paymentId'                  => ['required', 'string', 'max:255'],
            'issue_id'                   => ['required', 'integer', 'exists:issues,id'],
            'amount'                     => ['required', 'numeric', 'min:0.01'],
            'email'                      => ['required', 'email']
        ];
    }
}
