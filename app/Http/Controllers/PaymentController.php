<?php

namespace App\Http\Controllers;

use App\Actions\Payment\GetPaymentIntent;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Actions\Payment\ProcessPayment;

class PaymentController extends Controller
{
    public function getPaymentIntent(Request $request): JsonResponse
    {
        return GetPaymentIntent::get($request->get('amount'));
    }

    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        $validatedProcessPaymentData = $request->validated();

        return ProcessPayment::process(
            $validatedProcessPaymentData['pledgeExpirationDate'],
            $validatedProcessPaymentData['paymentId'],
            $validatedProcessPaymentData['issue_id'],
            $validatedProcessPaymentData['amount'],
            $validatedProcessPaymentData['email']
        );
    }
}