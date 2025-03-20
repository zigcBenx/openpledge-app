<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $feePercentage = (float) env('PLATFORM_FEE_PERCENTAGE', 0);

        DB::transaction(function () use ($feePercentage) {
            DB::table('donations')->get()->each(function ($donation) use ($feePercentage) {
                $grossAmount = (int) round($donation->amount * 100);
                $netAmount = (int) round($grossAmount - ($grossAmount / 100) * $feePercentage);

                DB::table('donations')->where('id', $donation->id)->update([
                    'gross_amount' => $grossAmount,
                    'net_amount' => $netAmount,
                    'fee_percentage' => $feePercentage
                ]);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // You can't revert that bruh :D
    }
};
