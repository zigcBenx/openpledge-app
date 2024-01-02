<?php

namespace App\Actions\Issue;

use App\Models\Issue;

class GetIssueById
{
    public static function get($id, $withDonationsSum = false)
    {
        $issue = Issue::with('repository', 'donations')->find($id);

        if ($withDonationsSum) {
            $issue->append('donation_sum');    
        }
        
        return $issue;
    }
}
