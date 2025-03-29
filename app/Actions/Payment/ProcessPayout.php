<?php

namespace App\Actions\Payment;

use App\Actions\Email\SendConnectStripeMail;
use App\Models\User;
use App\Actions\Email\SendIssueResolverMail;
use App\Actions\Email\SendNotifyPledgersMail;
use App\Models\Donation;
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

        $user->loadMissing('pendingWalletTransactions.donation');
        $payoutAmount = self::calculateAmountForPayout($user->pendingWalletTransactions);

        if (! self::hasEnoughFunds($payoutAmount)) {
            return response()->json(['error' => 'You do not have enough funds to payout'], 400);
        }

        $finalPayoutAmount = PayoutFeeService::calculate($payoutAmount);

        $donations = $user->pendingWalletTransactions->pluck('donation');
        $chargeId = self::getChargeId($donations);

        $transferId = self::makeTransfer($finalPayoutAmount, $user, $chargeId);

        self::markWalletTransactionsAsWithdrawn($user->pendingWalletTransactions);

        self::markDonationsAsPaidOut($donations, $transferId);
    }

    private static function calculateAmountForPayout($walletTransactions): float
    {
        return $walletTransactions->sum('amount');
    }

    private static function hasEnoughFunds($payoutAmount): bool
    {
        return PayoutFeeService::hasEnoughFunds($payoutAmount);
    }

    /**
     * We return the charge ID when there is a single donation for payment.
     * If there are multiple donations, we group them to reduce fees.
     * In such cases, the transfer is done without the charge ID as the source.
     *
     * @param $donations
     * @return mixed|null
     */
    private static function getChargeId($donations): mixed
    {
        if ($donations->count() > 1) return null;
        return $donations->first()->charge_id;
    }
    private static function makeTransfer($amount, $user, $charge_id = null)
    {
        return TransferFunds::transfer($user->stripe_id, $amount, $charge_id);
    }

    private static function markWalletTransactionsAsWithdrawn($pendingWalletTransactions): void
    {
        WalletTransaction::whereIn('id', $pendingWalletTransactions->pluck('id'))
            ->update(['is_withdrawn' => true, 'withdrawn_at' => Carbon::now()]);
    }

    private static function markDonationsAsPaidOut($donations, $transferId): void
    {
        Donation::whereIn('id', $donations->pluck('id'))
            ->update([
                'paid' => true,
                'payout_transaction_id' => $transferId
            ]);
    }
}
