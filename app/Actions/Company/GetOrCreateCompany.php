<?php

namespace App\Actions\Company;

use App\Models\Company;

class GetOrCreateCompany
{
    public static function get(string $companyName)
    {
        $companyName = strtoupper($companyName);

        $company = Company::firstOrCreate(['name' => $companyName]);

        return $company;
    }
}
