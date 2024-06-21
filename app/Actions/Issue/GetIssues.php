<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GetIssues
{
    /**
     * Get a collection of issues.
     *
     * @param bool $onlyWithDonations If true, only returns issues that have received valid donations.
     *                                Issues are joined with the donations table and only those with a sum of donations greater than 0 are included.
     *                                Valid donations are those where the expire_date is either not set or is greater than today.
     *                                If false, returns all issues with the sum of their donations.
     * @return \Illuminate\Database\Eloquent\Collection A collection of issues, with or without donations based on the parameter.
     */
    public static function get($onlyWithDonations = false)
    {
        if ($onlyWithDonations) {
            $today = Carbon::now()->toDateString();
            return Issue::query()
                ->leftJoin('donations', function ($join) use ($today) {
                    $join->on('donations.donatable_id', '=', 'issues.id')
                        ->where(function ($query) use ($today) {
                            $query->whereNull('donations.expire_date')
                                ->orWhere('donations.expire_date', '>', $today);
                        });
                })
                ->select('issues.*', DB::raw('SUM(donations.amount) as donations_sum_amount'))
                ->groupBy('issues.id')
                ->having(DB::raw('SUM(donations.amount)'), '>', 0)
                ->get();
        }

        return Issue::withSum('donations', 'amount', 'programmingLanguages')->get();
    }
}
