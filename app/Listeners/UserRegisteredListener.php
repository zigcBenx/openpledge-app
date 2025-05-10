<?php

namespace App\Listeners;

use App\Actions\WalletTransaction\CreateNewWalletTransaction;
use App\Models\PendingDonation;
use App\Services\GithubService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegisteredListener implements ShouldQueue
{

    public function handle(Registered $event): void
    {
        $user = $event->user;
        if (!$user->github_id) {
            // if user is not linked with GitHub, we can't make payout
            return;
        }

        $githubUser = $this->getGithubUserName($user);
        $donations = $this->getPendingDonations($githubUser);
        if ($donations->isEmpty()) {
            return;
        }
        $this->payoutToResolver($user, $donations);
        $this->markPendingDonationsAsClaimed($donations);
    }

    private function getPendingDonations($githubUser): \Illuminate\Support\Collection
    {
        return PendingDonation::with('donation')
            ->where('user_github_name', $githubUser['login'])
            ->where('is_claimed', false)
            ->get()
            ->pluck('donation');
    }

    private function getGithubUserName($user)
    {
        return GithubService::getUserByGithubId($user->github_id);
    }

    private function payoutToResolver($resolver, $donations): void
    {
        CreateNewWalletTransaction::create($resolver, $donations);
    }

    private function markPendingDonationsAsClaimed($donations): void
    {
        PendingDonation::whereIn('donation_id', $donations->pluck('id'))
            ->update(['is_claimed' => true, 'claimed_at' => Carbon::now()]);
    }
}
