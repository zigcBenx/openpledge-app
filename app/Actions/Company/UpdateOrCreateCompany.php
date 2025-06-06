<?php

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class UpdateOrCreateCompany
{
    public static function handle(
        string $companyName,
        string $companyAddress,
        string $companyCity,
        string $companyPostalCode,
        string $companyState,
        string $companyVatId,
        array $companyCountry,
    )
    {
        $companyName = strtoupper($companyName);

        $company = Company::updateOrCreate(['name' => $companyName], [
            'address' => $companyAddress,
            'vat_id' => $companyVatId,
            'city' => $companyCity,
            'postal_code' => $companyPostalCode,
            'state' => $companyState,
            'country' => $companyCountry,
        ]);

        if ($company->wasRecentlyCreated) {
            Auth::user()->update([
                'company_id' => $company->id,
            ]);
        }

        return $company;
    }

    public static function updateExisting(
        ?int $companyId,
        string $companyName,
        string $companyAddress,
        string $companyCity,
        string $companyPostalCode,
        string $companyState,
        string $companyVatId,
        array $companyCountry
    ) {
        if (!$companyId || !$company = Company::find($companyId)) {
            return self::handle(
                $companyName,
                $companyAddress,
                $companyCity,
                $companyPostalCode,
                $companyState,
                $companyVatId,
                $companyCountry
            );
        }

        $company->fill([
            'name' => $companyName,
            'address' => $companyAddress,
            'city' => $companyCity,
            'postal_code' => $companyPostalCode,
            'state' => $companyState,
            'vat_id' => $companyVatId,
            'country' => $companyCountry,
        ])->save();

        return $company;
    }
}
