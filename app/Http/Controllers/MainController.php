<?php

namespace App\Http\Controllers;

use App\Actions\Donation\Donators;
use App\Actions\Donation\GetDonations;
use App\Actions\User\Contributors;
use App\Models\ProgrammingLanguage;
use App\Actions\Issue\GetIssues;
use Illuminate\Support\Facades\Auth;
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

    public function saveRedirectPath(Request $request)
    {
        $redirectPath = $request->input('redirect_path');
        $redirectPathKey = $request->input('redirect_path_key');
        session([$redirectPathKey => $redirectPath]);
        return response()->json(['success' => true, 'message' => 'Redirect path saved successfully!']);
    }

    public function discoverIssues()
    {
        $user = Auth::user();
        $programmingLanguages = ProgrammingLanguage::select('id', 'name')->get();

        return Inertia::render('Discover/Issues', [
            'userIsContributor' => isset($user) ? $user->isContributor() : true,
            'userIsResolver' => isset($user) ? $user->isResolver() : true,
            'programmingLanguages' => $programmingLanguages
        ]);
    }

    public function filterIssues(Request $request)
    {
        return GetIssues::getWithFilters($request);
    }

    public function getTopContributors()
    {
        return Contributors::getTopContributors();
    }

    public function getTopDonors()
    {
        return Donators::getTopDonors();
    }

    public function getAnonymousDonations()
    {
        return GetDonations::getAnonymous();
    }
}
