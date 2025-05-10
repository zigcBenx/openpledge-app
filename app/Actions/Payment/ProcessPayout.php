<?php

namespace App\Actions\Payment;

use App\Models\WalletTransaction;
use App\Services\PayoutFeeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProcessPayout
{
    public static function process()
    {
        $user = Auth::user();

        if (! $user->isEligibleForPayout()) {
            return response()->json(['error' => 'You are not eligible for payout'], 400);
        }

        if ($user->hasPayoutThisMonth()) {
            return response()->json(['error' => 'You\'ve already made payout this month.'], 400);
        }

        $user->load('pendingWalletTransactions.donation');

        self::makeTransfers($user);

        self::markWalletTransactionsAsWithdrawn($user->pendingWalletTransactions);

        return response()->json(['message' => "Pay out to Stripe successfully"]);
    }

    private static function makeTransfers($user): void
    {
        $donationsToPayout = $user->pendingWalletTransactions->pluck('donation');

        foreach ($donationsToPayout as $idx => $donation) {
            $subtractMonthlyFee = false;
            if ($idx === 0) $subtractMonthlyFee = true;

            $amountToPay = PayoutFeeService::calculate($donation->net_amount, $subtractMonthlyFee);

            $transferId = TransferFunds::transfer($user->stripe_id, $amountToPay, $donation->charge_id);

            $donation->payout_transaction_id = $transferId;
            $donation->paid = true;
            $donation->save();
        }
    }


    private static function markWalletTransactionsAsWithdrawn($pendingWalletTransactions): void
    {
        WalletTransaction::whereIn('id', $pendingWalletTransactions->pluck('id'))
            ->update(['is_withdrawn' => true, 'withdrawn_at' => Carbon::now()]);
    }
}
