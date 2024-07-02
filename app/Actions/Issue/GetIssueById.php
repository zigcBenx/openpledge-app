<?php

namespace App\Actions\Issue;

use App\Models\Issue;

class GetIssueById
{
    public static function get($id)
    {
        return Issue::with('repository', 'donations.user')->find($id)->append('donation_sum');
    }
}
