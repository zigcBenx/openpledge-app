<?php

namespace App\Actions\Payment;

use Illuminate\Http\JsonResponse;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;

class GetPaymentIntent
{
    public static function get($donationAmount, $email): JsonResponse
    {
        if ($donationAmount == 0) {
            return response()->json(['error' => 'Donation amount cannot be 0.']);
        }

        $authUser = Auth::user();

        try {
            if ($authUser) {
                $customer = \Stripe\Customer::retrieve($authUser->id, []);
            } else {
                if (!$email) {
                    throw new \Exception('Email is required for anonymous donations');
                }
                
                try {
                    $customers = \Stripe\Customer::all([
                        'email' => $email,
                        'limit' => 1
                    ]);
                    $customer = $customers->data[0] ?? null;
                } catch (\Exception $e) {
                    $customer = null;
                }

                if (!$customer) {
                    $customer = \Stripe\Customer::create([
                        "email" => $email,
                    ]);
                }
            }
        } catch (\Exception $e) {
            if ($authUser) {
                $customer = \Stripe\Customer::create([
                    "id" => $authUser->id,
                    "name" => $authUser->name,
                    "email" => $authUser->email,
                ]);
            } else {
                $customer = \Stripe\Customer::create([
                    "email" => $email,
                ]);
            }
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => number_format($donationAmount, 2, "", ""),
            'currency' => config('app.stripe_currency'),
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'customer' => $customer->id
        ]);

        return new JsonResponse(['paymentId' => $paymentIntent->id, 'clientSecret' => $paymentIntent->client_secret]);
    }
}
