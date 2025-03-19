<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use Carbon\Carbon;

class GetAvailableDonationsForIssue
{
    public static function get($issue)
    {
        return Donation::where('donatable_id', $issue->id)
            ->where(function ($query) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', Carbon::now());
            })
            ->where('paid', false)
            ->with('user')
            ->get();
    }
}
