<?php

namespace App\Actions\Payment;

use App\Actions\Donation\CreateNewDonation;
use App\Services\DonationFeeService;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;
use App\Actions\Issue\GetIssueById;
use App\Actions\Comment\ConstructComment;
use App\Actions\Email\SendNewPledgeMail;
use App\Services\GithubService;
use Carbon\Carbon;

class ProcessPayment
{
    public static function process($pledgeExpirationDate, $paymentId, $issueId, $amount, $donorEmail, $isAuthenticated, $isPledgingAnonymously, $shouldBillCompany = false): JsonResponse
    {
        $expireDate = null;
        if (isset($pledgeExpirationDate)) {
            $expireDate = date('Y-m-d', strtotime($pledgeExpirationDate));
        }

        $afterFeeAmounts = DonationFeeService::calculateAmounts($amount);

        $donationData = self::prepareDonationDate($afterFeeAmounts, $issueId, $expireDate, $isPledgingAnonymously);

        self::createDonation($paymentId, $donationData, $shouldBillCompany);

        $issue = GetIssueById::getWithActiveDonations($issueId);
        $comment = self::prepareGithubComment($issue, $afterFeeAmounts['net_amount'], $isAuthenticated, $isPledgingAnonymously, $expireDate);

        GithubService::commentOnIssue($issue, $comment);
        SendNewPledgeMail::send($donorEmail, Auth::user()->name ?? "Anonymous Pledger", $issueId, $afterFeeAmounts['net_amount']);

        return new JsonResponse(['success' => true]);
    }

    private static function prepareDonationDate($afterFeeAmounts, $issueId, $expireDate, $isPledgingAnonymously): array
    {
        return array_merge(
            $afterFeeAmounts,
            [
                'donatable_type' => Issue::class,
                'donatable_id' => $issueId,
                'expire_date' => $expireDate,
                'donor_id' => $isPledgingAnonymously ? null : Auth::id(),
            ]
        );
    }

    private static function createDonation(string $paymentId, array $donationData, bool $shouldBillCompany = false): void
    {
        $stripe = new StripeClient(config('app.stripe_secret'));
        $paymentDetail = $stripe->paymentIntents->retrieve($paymentId);

        $donationData['transaction_id'] = $paymentDetail->id;
        $donationData['charge_id'] = $paymentDetail->latest_charge;

        if ($shouldBillCompany) {
            $donationData['company_id'] = Auth::user()->company_id;
        }

        CreateNewDonation::create($donationData);
    }

    private static function prepareGithubComment($issue, float $amount, bool $isAuthenticated, bool $isPledgingAnonymously, ?string $expireDate): string
    {
        $donorName = ($isAuthenticated && !$isPledgingAnonymously) ? Auth::user()->name : "Anonymous Pledger";
        $formattedExpireDate = $expireDate ? Carbon::parse($expireDate)->format('F j, Y') : null;
        $totalBounty = $issue->donations_sum_net_amount;
        $existingPledge = ($issue->donations_sum_net_amount - $amount) > 0;

        if ($existingPledge) {
            return ConstructComment::constructShortPledgeComment($amount, $donorName, $issue->id, $totalBounty, $formattedExpireDate);
        }

        return ConstructComment::constructPledgeComment($amount, $donorName, $issue->id, $formattedExpireDate);
    }
}
