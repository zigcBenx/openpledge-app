<?php

namespace App\Actions\Payment;

use App\Actions\Donation\CreateNewDonation;
use App\Services\DonationFeeService;
use App\Actions\Email\SendConnectStripeMail;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;
use App\Actions\Issue\GetIssueById;
use App\Actions\Comment\ConstructComment;
use App\Actions\Donation\GetAvailableDonationsForIssue;
use App\Actions\Email\SendNewPledgeMail;
use Stripe\Exception\ApiErrorException;
use App\Services\GithubService;
use App\Actions\Email\SendIssueResolverMail;
use App\Actions\Email\SendNotifyPledgersMail;
use App\Models\Donation;
use Carbon\Carbon;

class ProcessPayment
{
    public static function process($pledgeExpirationDate, $paymentId, $issueId, $amount, $donorEmail, $isAuthenticated, $isPledgingAnonymously): JsonResponse
    {
        $expireDate = null;
        if (isset($pledgeExpirationDate)) {
            $expireDate = date('Y-m-d', strtotime($pledgeExpirationDate));
        }

        $afterFeeAmounts = DonationFeeService::calculateAmounts($amount);

        $donationData = self::prepareDonationDate($afterFeeAmounts, $issueId, $expireDate, $isPledgingAnonymously);

        self::createDonation($paymentId, $donationData);

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

    private static function createDonation(string $paymentId, array $donationData): void
    {
        $stripe = new StripeClient(config('app.stripe_secret'));
        $paymentDetail = $stripe->paymentIntents->retrieve($paymentId);

        $donationData['transaction_id'] = $paymentDetail->id;
        $donationData['charge_id'] = $paymentDetail->latest_charge;

        CreateNewDonation::create($donationData);
    }

    private static function prepareGithubComment($issue, int $amount, bool $isAuthenticated, bool $isPledgingAnonymously, ?string $expireDate): string
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

    // TODO: Move this to separate action
    public static function processDonations(Issue $issue, User $dbUser)
    {
        $donations = GetAvailableDonationsForIssue::get($issue);

        if ($donations->isEmpty()) {
            logger('[INFO] No unpaid donations found for issue', ['issue_id' => $issue->id]);
            return;
        }

        $donationsSumAmount = $donations->sum('amount'); // TODO: rework this
        $feePercentage = config('app.platform_fee_percentage');
        $totalPayoutAmount = $donationsSumAmount - $donationsSumAmount * ($feePercentage / 100);
        $resolverMail = $dbUser->email;
        $destinationStripeId = $dbUser->stripe_id;
        $issueId = $issue->id;

        if (isset($destinationStripeId)) {
            $transferIds = [];

            foreach ($donations as $donation) {
                if (!$donation->charge_id) {
                    logger('[WARNING] Donation missing charge_id', [
                        'donation_id' => $donation->id,
                        'issue_id' => $issue->id
                    ]);
                    continue;
                }

                try {
                    $transferAmount = $donation->amount - $donation->amount * ($feePercentage / 100);
                    $transferId = TransferFunds::transfer($destinationStripeId, $transferAmount, $donation->charge_id);
                    $transferIds[] = $transferId;

                    Donation::where('charge_id', $donation->charge_id)
                        ->update(['paid' => true, 'payout_transaction_id' => $transferId]);
                } catch (\Exception $e) {
                    logger('[ERROR] Failed to transfer funds for donation', [
                        'donation_id' => $donation->id,
                        'charge_id' => $donation->charge_id,
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            logger('[INFO] Funds transferred', [
                'issue_id' => $issue->id,
                'amount' => $totalPayoutAmount,
                'stripe_transfer_ids' => $transferIds,
                'successful_transfers' => count($transferIds)
            ]);
        } else {
            SendConnectStripeMail::send($resolverMail, $dbUser->name, $totalPayoutAmount, "Payout");
            logger('[WARNING] Cannot transfer funds: User does not have a connected Stripe account.', ['user_id' => $dbUser->id]);
        }

        // Query users who have this issue as an active issue but exclude the current resolver
        $usersWithActiveIssue = User::whereHas('active_issues', function ($query) use ($issueId) {
            $query->where('issue_id', $issueId);
        })->where('email', '!=', $resolverMail)->get();

        SendIssueResolverMail::send($resolverMail, $dbUser->name, $issue->id, $usersWithActiveIssue); // Send emails to all resolvers during beta, regardless of Stripe connection

        // Send emails to users that pledged to this issue notifying them that the issue has been resolved
        $pledgers = $donations->pluck('user')->filter()->values()->toArray();
        SendNotifyPledgersMail::send($pledgers, $issue->id);
    }
}
