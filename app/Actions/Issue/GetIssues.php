<?php

namespace App\Actions\Issue;

use App\Models\Issue;

class GetIssues
{
    public static function get()
    {
        return Issue::withSum('donations', 'amount')->get();
    }
}
