<?php

namespace App\Listeners;

use App\Actions\WalletTransaction\CreateNewWalletTransaction;
use App\Models\PendingDonation;
use App\Services\GithubService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegisteredListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        if (!$user->github_id) {
            // if user is not linked with GitHub, we can't make payout
            return;
        }
        logger("Getting donations");

        $githubUser = self::getGithubUserName($user);
        logger($githubUser);
        $donations = self::getPendingDonations($githubUser);
        logger($donations);
        self::payoutToResolver($user, $donations);
        logger();
        logger('Finished payout to resolver to wallet on registration');
    }

    private static function getPendingDonations($githubUser)
    {
        logger("Github user id" . $githubUser['id']);
        return PendingDonation::with('donation')
            ->where('user_github_name', $githubUser['login'])
            ->get()
            ->pluck('donation');
    }

    private static function getGithubUserName($user)
    {
        return GithubService::getUserByGithubId($user->github_id);
    }

    private static function payoutToResolver($resolver, $donations): void
    {
        CreateNewWalletTransaction::create($resolver, $donations);
    }
}
