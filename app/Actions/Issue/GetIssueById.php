<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Carbon\Carbon;

class GetIssueById
{
    public static function get($id)
    {
        $issue = Issue::with([
            'repository.programmingLanguages',
            'donations.user',
            'userFavorite',
        ])->find($id)->append('donation_sum');

        $issue->favorite = $issue->userFavorite->isNotEmpty();
        $issue->isAuthUsersActiveIssue = $issue->isAuthUsersActiveIssue();

        return $issue;
    }

    public static function getWithActiveDonations($id)
    {
        $today = Carbon::now()->toDateString();

        return Issue::withSum([
            'donations' => function ($query) use ($today) {
                $query->where(function ($query) use ($today) {
                    $query->whereNull('expire_date')
                        ->orWhere('expire_date', '>', $today);
                });
            }
        ], 'amount')->find($id);
    }
}
