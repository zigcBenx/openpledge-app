<?php

namespace App\Actions\Payment;

use App\Actions\Email\SendConnectStripeMail;
use App\Models\User;
use App\Actions\Email\SendIssueResolverMail;
use App\Actions\Email\SendNotifyPledgersMail;
use App\Models\Donation;
use App\Services\PayoutFeeService;
use Illuminate\Support\Facades\Auth;

class ProcessPayout
{
    public static function process()
    {
        $user = Auth::user();

        if (!$user->isEligibleForPayout()) {
            // show alert that and why he is not eligible
            return response()->json(['error' => 'You are not eligible for payout'], 400);

            // TODO: Is this useful anywhere?
//            SendConnectStripeMail::send($resolverMail, $dbUser->name, $totalPayoutAmount, "Payout");
//            logger('[WARNING] Cannot transfer funds: User does not have a connected Stripe account.', ['user_id' => $dbUser->id]);
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
        // todo: add this transfer id to all donations payout_transaction_id that were paid out



        // Should we store those transfers in out database also?
            // if you check donations payout_transaction_id you actually already have this info

        // Flag wallet transactions that they were paid out
        $user->pendingWalletTransactions->update(['is_withdrawn' => true]);
        // Flag donations that they were paid out
        $donations->update(['paid' => true]);
    }

    private static function calculateAmountForPayout($walletTransactions): float
    {
        return $walletTransactions->sum('amount');
    }

    private static function hasEnoughFunds($payoutAmount): bool
    {
        // TODO: Add condition for minimum payout amount
        return PayoutFeeService::hasEnoughFunds($payoutAmount) > 0.0;
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
}
