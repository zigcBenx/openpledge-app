<?php

namespace App\Actions\WalletTransaction;

use Carbon\Carbon;

class GetWalletTransactionsForAuthUser
{
    public static function get()
    {
        return auth()->user()->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id'           => $transaction->id,
                    'amount'       => $transaction->amount,
                    'is_withdrawn' => $transaction->is_withdrawn,
                    'created_at'   => $transaction->created_at->format('Y-m-d H:i'),
                    'withdrawn_at' => Carbon::parse($transaction->withdrawn_at)->format('Y-m-d H:i')
                ];
            });
    }
}
