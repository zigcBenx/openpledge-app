<?php

namespace App\Actions\Payment;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Inertia\Inertia;

class StripeConnect
{
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

        Auth::user()->update(['stripe_id' => $stripeId]);
        return Inertia::render('ConnectStripe');
    }

    public static function getDashboardLink()
    {
        $loginLink = Account::createLoginLink(Auth::user()->stripe_id);
        return response()->json(['url' => $loginLink->url]);
    }
}
