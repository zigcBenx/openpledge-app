<?php

namespace App\Http\Controllers;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index(Request $request)
    {
        // Replace 'php' with the desired language
        $language = 'php';

        // Define the search query
        $query = 'language:' . $language . ' is:issue';

        // Use the GitHub API to search for issues
        $issues = GitHub::search()->issues($query, 1);
        // $issues = GitHub::issues();
        // logger($issues);
        // $repos = $client->api('repo')->find('chess', array('language' => 'php'));
        return Inertia::render('Dashboard', [
            'issues' => $issues
        ]);
    }
}
