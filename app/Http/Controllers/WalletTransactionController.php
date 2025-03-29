<?php

namespace App\Http\Controllers;

use App\Actions\WalletTransaction\GetWalletTransactionsForAuthUser;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletTransactionController extends Controller
{
    public function index()
    {
        $transactions = GetWalletTransactionsForAuthUser::get();

        $canPayout = ! Auth::user()->hasPayoutThisMonth();

        return Inertia::render('Wallet/Index', [
            'transactions' => $transactions,
            'canPayout' => $canPayout,
        ]);
    }
}
