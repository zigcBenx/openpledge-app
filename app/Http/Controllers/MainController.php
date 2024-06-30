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
        $filters = $request->query();
        $issues = GetIssues::getWithActiveDonations($filters);
        return Inertia::render('Discover/Issues', [
            'issues' => $issues
        ]);
    }
}
