<?php

namespace App\Http\Controllers;

use App\Actions\Issue\GetIssues;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Home');
    }

    public function dashboard(Request $request)
    {
        return Inertia::render('Dashboard');
    }

    public function discoverIssues(Request $request)
    {
        $issues = GetIssues::getWithActiveDonations();
        return Inertia::render('Discover/Issues', [
            'issues' => $issues
        ]);
    }
}
