<?php

namespace App\Actions\Payment;
use Illuminate\Http\JsonResponse;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;

class GetPaymentIntent
{
    public static function get($donationAmount): JsonResponse
    {
        $authUser = Auth::user();

        try {
            $customer = \Stripe\Customer::retrieve($authUser->id, []);
        } catch (\Exception $e) {
            $customer = \Stripe\Customer::create([
                "id" => $authUser->id,
                "name" => $authUser->name,
                "email" => $authUser->email,
            ]);
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => number_format($donationAmount, 2, "", ""),
            'currency' => config('app.stripe_currency'),
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'customer' => $customer->id,
            'capture_method' => 'manual',
            // 'payment_method' => '{{PAYMENT_METHOD_ID}}', // id of selected card
        ]);

        return new JsonResponse(['paymentId' => $paymentIntent->id, 'clientSecret' => $paymentIntent->client_secret]);
    }
}
