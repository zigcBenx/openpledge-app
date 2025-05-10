<?php

namespace App\Actions\WalletTransaction;

class CreateNewWalletTransaction
{
    public static function create($user, $donations)
    {
        foreach ($donations as $donation) {
            $donation->walletTransactions()->create([
                'amount' => $donation->net_amount,
                'is_withdrawn' => false,
                'contributor_id' => $user->id,
            ]);
        }
    }
}
