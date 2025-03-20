<?php

namespace App\Http\Controllers;

use App\Actions\WalletTransaction\GetWalletTransactionsForAuthUser;
use Inertia\Inertia;

class WalletTransactionController extends Controller
{
    public function index()
    {
        $transactions = GetWalletTransactionsForAuthUser::get();

        return Inertia::render('Wallet/Index', [
            'transactions' => $transactions
        ]);
    }
}
