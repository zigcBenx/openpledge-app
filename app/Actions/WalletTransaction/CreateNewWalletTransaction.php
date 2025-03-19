<?php

namespace App\Actions\WalletTransaction;

use App\Actions\Donation\GetAvailableDonationsForIssue;

class CreateNewWalletTransaction
{
    public static function create($issue, $user)
    {
        logger("Is creating wallet transactions for issue {$issue->id} and user {$user->id}");
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