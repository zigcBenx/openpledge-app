<?php

namespace App\Actions\Company;

use App\Models\Company;

class UpdateOrCreateCompany
{
    public static function handle(string $companyName, string $companyAddress, string $companyVatId, bool $shouldBillCompany = true)
    {
        $companyName = strtoupper($companyName);

        $company = Company::updateOrCreate(['name' => $companyName], [
            'address' => $companyAddress,
            'vat_id' => $companyVatId,
            'should_bill_company' => $shouldBillCompany,
        ]);

        return $company;
    }

    public static function updateExisting(int $companyId, string $companyName, string $companyAddress, string $companyVatId, bool $shouldBillCompany)
    {
        $company = Company::find($companyId);

        if (!$company) {
            return self::handle($companyName, $companyAddress, $companyVatId, $shouldBillCompany);
        }

        $company->name = $companyName;
        $company->address = $companyAddress;
        $company->vat_id = $companyVatId;
        $company->should_bill_company = $shouldBillCompany;
        $company->save();

        return $company;
    }
}
