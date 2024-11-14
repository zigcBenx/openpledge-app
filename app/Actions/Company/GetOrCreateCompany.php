<?php

namespace App\Actions\Company;

use App\Models\Company;

class GetOrCreateCompany
{
    public static function getId(string $companyName)
    {
        $companyName = strtoupper($companyName);

        $company = Company::firstOrCreate(['name' => $companyName]);

        return $company->id;
    }
}
