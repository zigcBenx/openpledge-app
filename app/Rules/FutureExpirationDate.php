<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class FutureExpirationDate implements ValidationRule
{
    protected $dayMonth;
    public function __construct($dayMonth)
    {
        $this->dayMonth = $dayMonth;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->dayMonth || !preg_match('/^\d{2}\/\d{2}$/', $this->dayMonth)) {
            $fail('The expiration date format is invalid.');
            return;
        }

        [$day, $month] = explode('/', $this->dayMonth);
        
        try {
            $expirationDate = Carbon::createFromDate($value, $month, $day, 'UTC');

            if (!$expirationDate->isFuture()) {
                $fail('The expiration date must be a valid future date.');
            }
        } catch (\Exception $e) {
            $fail('The expiration date is invalid.');
        }
    }
}