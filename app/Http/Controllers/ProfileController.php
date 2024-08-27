<?php

namespace App\Http\Controllers;

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

    public function getAuthUsersActiveIssues()
    {
        $userId = Auth::id();
        return GetUsersActiveIssues::get($userId);
    }

    public function getAuthUsersFinishedIssues()
    {
        $user = Auth::user();
        $githubId = $user->github_id;
        return GetUsersFinishedIssues::get($githubId);
    }
}
