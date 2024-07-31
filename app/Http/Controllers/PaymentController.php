<?php

namespace App\Http\Controllers;

use App\Actions\Email\SendNewPledgeMail;
use App\Actions\Donation\CreateNewDonation;
use App\Actions\Issue\GetIssueById;
use App\Http\Requests\StoreProcessPaymentRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use App\Actions\Github\CommentOnIssue;

class PaymentController extends Controller
{
    public function getPaymentIntent(Request $request): JsonResponse
    {
        $authUser = request()->user();

        $donationAmount = $request->get('amount');

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

    public function process(StoreProcessPaymentRequest $request): JsonResponse
    {
        try {
            $expireDate = null;
            if($request->get('pledgeExpirationDate') && $request->get('pledgeExpirationYear')) {
                // Extract day and month from 'DD/MM' format & combine with year to form 'Y-m-d' format
                list($day, $month) = explode('/', $request->get('pledgeExpirationDate')['value']);
                $expireDate = date('Y-m-d', strtotime("{$request->get('pledgeExpirationYear')}-$month-$day"));
            }

            $stripe = new StripeClient(config('app.stripe_secret'));
            $paymentDetail = $stripe->paymentIntents->retrieve($request->get('paymentId'));
            $issueId = $request->get('issue_id');

            $donation = CreateNewDonation::create(
                [
                    'donatable_type' => Issue::class,
                    'donatable_id' => $issueId,
                    'amount' => $request->get('amount'),
                    'transaction_id' => $paymentDetail->id,
                    'donor_id' => Auth::id(),
                    'expire_date' => $expireDate,
                ]
            );

            $issue = GetIssueById::get($issueId);

            list($owner, $repo) = explode('/', $issue['repository']['title']);
            $issueNumber = basename(parse_url($issue['github_url'], PHP_URL_PATH));

            $installationId = $issue['repository']['githubInstallation']['installation_id'];
            $token = CommentOnIssue::getInstallationAccessToken($installationId);

            $amount = $request->get('amount');
            $donorEmail = $request->get('email');
            $donorName = Auth::user()->name;
            $comment = CommentOnIssue::constructPledgeComment($amount, $donorName, $issueId);

            CommentOnIssue::run($token, $owner, $repo, $issueNumber, $comment);
            SendNewPledgeMail::send($donorEmail, $donorName, $issueId);

            return new JsonResponse(['success' => true]);
        } catch (ApiErrorException $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}