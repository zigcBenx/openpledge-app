<?php

namespace App\Actions\Github;

use GrahamCampbell\GitHub\Facades\GitHub;

class GetGithubRepositories
{
    /**
     * Delete the given user.
     */
    public static function run()
    {
        // Replace 'php' with the desired language
        $language = 'php';

        // Define the search query
        $query = 'language:' . $language;

        // Use the GitHub API to search for issues
        return GitHub::search()->repositories($query, 1);
    }
}
