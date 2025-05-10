<?php

namespace App\Actions\Payment;

use App\Models\Issue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Inertia\Inertia;
use App\Models\Donation;
use Carbon\Carbon;

class StripeConnect
{
    public static function stripeConnect()
    {
        if (!Auth::user()->github_id) {
            return Inertia::render('Error', [
                'message' => 'You must connect your GitHub account before you can connect Stripe.',
                'subMessage' => 'Connect your GitHub account to continue by clicking the button below and following the instructions.',
                'redirectUrl' => route('github.auth.redirect'),
                'redirectButtonText' => 'Connect GitHub',
                'actionUrl' => route('save-redirect-path'),
                'actionData' => [
                    'redirect_path' => route('stripe.connect', [], false),
                    'redirect_path_key' => 'github_redirect_path'
                ]
            ]);
        }

        return Inertia::render('ConnectStripe');
    }

    public static function createAccountLink(Request $request)
    {
        $countryCode = $request->input('country_code');
        $businessType = $request->input('business_type');
        $request->session()->forget('stripe_id');
        Stripe::setApiKey(config('app.stripe_secret'));

        $account = Account::create([
            'country' => $countryCode,
            'type' => 'express',
            'business_type' => $businessType,
            'capabilities' => [
                'transfers' => ['requested' => true],
                'card_payments' => ['requested' => true],
            ],
        ]);

        $request->session()->put('stripe_id', $account->id);

        $accountLink = AccountLink::create([
            'account' => $account->id,
            'refresh_url' => route('stripe.onboarding.refresh'),
            'return_url' => route('stripe.onboarding.return'),
            'type' => 'account_onboarding',
        ]);

        return response()->json(['url' => $accountLink->url]);
    }

    public static function handleOnboardingRefresh(Request $request)
    {
        $request->session()->forget('stripe_id');
        return Inertia::render('Error', [
            'message' => 'The Stripe onboarding link has expired or was already used.',
            'redirectRoute' => 'stripe.connect',
            'redirectButtonText' => 'Try again'
        ]);
    }

    public static function handleOnboardingReturn(Request $request)
    {
        if(Auth::user()->stripe_id) {
            return Inertia::render('ConnectStripe');
        }

        $stripeId = $request->session()->get('stripe_id');
        $request->session()->forget('stripe_id');

        if (!isset($stripeId)) {
            return Inertia::render('Error', [
                'message' => 'Error during Stripe onboarding.',
                'redirectRoute' => 'stripe.connect',
                'redirectButtonText' => 'Try again'
            ]);
        }

        Stripe::setApiKey(config('app.stripe_secret'));
        $account = Account::retrieve($stripeId);

        if (isset($account->requirements->disabled_reason)) {
            return Inertia::render('Error', [
                'message' => 'Stripe onboarding incomplete!',
                'redirectRoute' => 'stripe.connect',
                'redirectButtonText' => 'Try again'
            ]);
        }

        $authenticatedUser = Auth::user();
        $authenticatedUser->stripe_id = $stripeId;
        $authenticatedUser->save();

        $today = Carbon::now()->toDateString();
        $unpaidRewards = Donation::where('donatable_type', Issue::class)
            ->whereHas('donatable', function($query) use ($authenticatedUser, $today) {
                $query->where('resolver_github_id', $authenticatedUser->github_id);
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', $today);
            })
            ->where('paid', false)
            ->get();

        $unpaidRewardsSumAmount = $unpaidRewards->sum('net_amount');
        $feePercentage = config('app.platform_fee_percentage');
        $totalPayoutAmount = $unpaidRewardsSumAmount - $unpaidRewardsSumAmount * ($feePercentage / 100);

        foreach ($unpaidRewards as $unpaidReward) {
            if (!$unpaidReward->charge_id) {
                logger('[WARNING] Donation missing charge_id', [
                    'donation_id' => $unpaidReward->id
                ]);
                continue;
            }

            try { // TODO:
                $transferAmount = $unpaidReward->amount - $unpaidReward->amount * ($feePercentage / 100);
                $transferId = TransferFunds::transfer($stripeId, $transferAmount, $unpaidReward->charge_id);
                $transferIds[] = $transferId;

                $unpaidReward->paid = true;
                $unpaidReward->payout_transaction_id = $transferId;
                $unpaidReward->save();
            } catch (\Exception $e) {
                logger('[ERROR] Failed to transfer funds for donation', [
                    'donation_id' => $unpaidReward->id,
                    'charge_id' => $unpaidReward->charge_id,
                    'stack_trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }

        if (isset($transferIds) && count($transferIds) > 0) {
            logger('[INFO] Funds transferred', [
                'amount' => $totalPayoutAmount,
                'stripe_transfer_ids' => $transferIds,
                'successful_transfers' => count($transferIds)
            ]);
        }

        return Inertia::render('ConnectStripe');
    }

    public static function getDashboardLink()
    {
        $loginLink = Account::createLoginLink(Auth::user()->stripe_id);
        return response()->json(['url' => $loginLink->url]);
    }
}
