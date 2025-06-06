<?php

namespace App\Http\Controllers;

use App\Actions\Company\UpdateOrCreateCompany;
use App\Actions\Profile\UpdateProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Http\Controllers\Inertia\Concerns\ConfirmsTwoFactorAuthentication;
use Laravel\Jetstream\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Actions\Repository\GetInstalledRepositories;
use App\Actions\Favorite\GetFavorites;
use App\Actions\Issue\GetUsersActiveIssues;
use App\Actions\Issue\GetUsersFinishedIssues;
use Illuminate\Support\Facades\Auth;

/**
 * NOTE: Most of this code was copy pasted from vendor\Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController.php
 * So we can override profile routes.
 */
class ProfileController extends Controller
{
    use ConfirmsTwoFactorAuthentication;

    public function show(Request $request)
    {
        return Inertia::render('Profile/Show');
    }

    public function settings(Request $request)
    {
        $this->validateTwoFactorAuthenticationState($request);

        return Jetstream::inertia()->render($request, 'Profile/Settings', [
            'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
            'sessions' => $this->sessions($request)->all(),
        ]);
    }

    /**
     * Get the current sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function sessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $request->user()->getAuthIdentifier())
                    ->orderBy('last_activity', 'desc')
                    ->get()
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Laravel\Jetstream\Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent(), fn ($agent) => $agent->setUserAgent($session->user_agent));
    }

    public function getInstalledRepositories()
    {
        return GetInstalledRepositories::get();
    }

    public function getFavorites()
    {
        return GetFavorites::get();
    }

    public function showAuthUsersFavorites(Request $request)
    {
        $page = $request->input('page', 1);
        $showIssues = filter_var($request->input('showIssues', true), FILTER_VALIDATE_BOOLEAN);

        $favorites = GetFavorites::getPaginated($page, $showIssues);

        if ($page === 1) {
            return Inertia::render('Profile/ShowAll', [
                'issues' => $showIssues ? $favorites->items() : [],
                'repositories' => !$showIssues ? $favorites->items() : [],
                'noIssuesMessage' => 'You have no favorites',
                'title' => 'Favorites',
                'description' => "Issues and repositories you’ve claimed as your own...",
                'routeName' => 'profile.favorites-show',
                'current_page' => $favorites->currentPage(),
                'isFavoritesPage' => true,
                'last_page' => $favorites->lastPage(),
            ]);
        }

        return response()->json([
            'issues' => $showIssues ? $favorites->items() : [],
            'repositories' => !$showIssues ? $favorites->items() : [],
            'current_page' => $favorites->currentPage(),
            'last_page' => $favorites->lastPage(),
        ]);
    }

    public function getAuthUsersActiveIssues()
    {
        $userId = Auth::id();
        return GetUsersActiveIssues::get($userId);
    }

    public function showAuthUsersActiveIssues(Request $request)
    {
        $userId = Auth::id();
        $page = $request->input('page', 1);

        $issues = GetUsersActiveIssues::getPaginated($userId, $page);

        if ($page === 1) {
            return Inertia::render('Profile/ShowAll', [
                'issues' => $issues->items(),
                'noIssuesMessage' => 'You have no active issues',
                'title' => 'Work In Progress',
                'description' => "Issues you're currently wrestling...",
                'routeName' => 'profile.actives-show',
                'current_page' => $issues->currentPage(),
                'last_page' => $issues->lastPage(),
            ]);
        }

        return response()->json([
            'issues' => $issues->items(),
            'current_page' => $issues->currentPage(),
            'last_page' => $issues->lastPage(),
        ]);
    }

    public function getAuthUsersFinishedIssues()
    {
        $user = Auth::user();
        $githubId = $user->github_id;
        return GetUsersFinishedIssues::get($githubId);
    }

    public function showAuthUsersFinishedIssues(Request $request)
    {
        $user = Auth::user();
        $githubId = $user->github_id;
        $page = $request->input('page', 1);

        $issues = GetUsersFinishedIssues::getPaginated($githubId, $page);

        if ($page === 1) {
            return Inertia::render('Profile/ShowAll', [
                'issues' => $issues->items(),
                'noIssuesMessage' => 'You have no finished issues',
                'title' => 'Finished',
                'description' => "Issues you've tamed and sent to bug heaven...",
                'routeName' => 'profile.finished-show',
                'current_page' => $issues->currentPage(),
                'last_page' => $issues->lastPage(),
            ]);
        }

        return response()->json([
            'issues' => $issues->items(),
            'current_page' => $issues->currentPage(),
            'last_page' => $issues->lastPage(),
        ]);
    }

    public function updateAnonymousPledging(Request $request)
    {
        return UpdateProfile::toggleAnonymousPledging($request->input('is_pledging_anonymously'));
    }

    public function updateCompany(Request $request)
    {
        UpdateOrCreateCompany::updateExisting(
            $request->input('companyId'), 
            $request->input('companyName'), 
            $request->input('companyAddress'), 
            $request->input('companyCity'),
            $request->input('companyPostalCode'),
            $request->input('companyState'),
            $request->input('companyVatId'),
            $request->input('companyCountry'),
        );
    }
}
