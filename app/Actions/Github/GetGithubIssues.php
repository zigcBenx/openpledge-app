<?php

namespace App\Actions\Github;

use GrahamCampbell\GitHub\Facades\GitHub;

class GetGithubIssues
{
    /**
     * Fetch issues from Github according to passed query string
     */
    public static function run($query)
    {
        return GitHub::search()->issues($query, 1);
    }
}
