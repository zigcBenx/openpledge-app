<?php

namespace App\Http\Controllers;

use App\Actions\Payment\GetPaymentIntent;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Actions\Payment\ProcessPayment;
use Illuminate\Support\Facades\Auth;
class PaymentController extends Controller
{
    public function getPaymentIntent(Request $request): JsonResponse
    {
        return GetPaymentIntent::get($request->get('amount'), $request->get('email'));
    }

    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        $user = Auth::user();
        $isAuthenticated = isset($user);

        if ($isAuthenticated) {
            $request->merge(['email' => $user->email]);
        }

        $validatedProcessPaymentData = $request->validated();

        return ProcessPayment::process(
            $validatedProcessPaymentData['pledgeExpirationDate'],
            $validatedProcessPaymentData['paymentId'],
            $validatedProcessPaymentData['issue_id'],
            $validatedProcessPaymentData['amount'],
            $validatedProcessPaymentData['email'],
            $isAuthenticated
        );
    }
}