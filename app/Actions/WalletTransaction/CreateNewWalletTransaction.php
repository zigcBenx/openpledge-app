<?php

namespace App\Actions\WalletTransaction;

use App\Actions\Donation\GetAvailableDonationsForIssue;

class CreateNewWalletTransaction
{
    public static function create($issue, $user)
    {
        $donations = GetAvailableDonationsForIssue::get($issue);

        foreach ($donations as $donation) {
            $donation->walletTransactions()->create([
                'amount' => $donation->amount,
                'is_withdrawn' => false,
                'contributor_id' => $user->id,
            ]);
        }
    }
}
