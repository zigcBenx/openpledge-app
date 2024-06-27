<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GetIssues
{
    public static function get()
    {
        return Issue::withSum('donations', 'amount', 'programmingLanguages')->get();
    }

    public static function getWithActiveDonations()
    {
        $today = Carbon::now()->toDateString();

        return Issue::query()
            ->with('programmingLanguages')
            ->withSum(['donations' => function ($query) use ($today) {
                $query->where(function ($query) use ($today) {
                    $query->whereNull('expire_date')
                        ->orWhere('expire_date', '>', $today);
                });
            }], 'amount')
            ->having('donations_sum_amount', '>', 0)
            ->get();
    }
}
