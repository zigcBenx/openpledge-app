<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function getPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $authUser = request()->user();

        $donationAmount = $request->get('donation');

        try {
            $customer = \Stripe\Customer::retrieve($authUser->id, []);
        } catch (\Exception $e) {
            $customer = \Stripe\Customer::create([
                "id" => $authUser->id,
                "name" => $authUser->name,
                "email" => $authUser->email,
                // "phone" => $$authUser->phone,
            ]);
        }

        // setup payment intent
        $paymentIntent = PaymentIntent::create([
            'amount' => number_format($donationAmount, 2, "", ""),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'customer' => $customer->id,
            // 'payment_method' => '{{PAYMENT_METHOD_ID}}', // id of selected card
        ]);

        return ['clientSecret' => $paymentIntent->client_secret];
    }
}