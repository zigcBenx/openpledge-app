<?php

namespace App\Actions\Payment;

use App\Actions\Donation\CreateNewDonation;
use App\Actions\Email\SendConnectStripeMail;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;
use App\Actions\Issue\GetIssueById;
use App\Actions\Comment\ConstructComment;
use App\Actions\Email\SendNewPledgeMail;
use Stripe\Exception\ApiErrorException;
use App\Services\GithubService;
use App\Actions\Email\SendIssueResolverMail;
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

        try {
            $stripe = new StripeClient(config('app.stripe_secret'));
            $paymentDetail = $stripe->paymentIntents->retrieve($paymentId);
            CreateNewDonation::create(
                [
                    'donatable_type' => Issue::class,
                    'donatable_id' => $issueId,
                    'amount' => $amount,
                    'transaction_id' => $paymentDetail->id,
                    'charge_id' => $paymentDetail->latest_charge,
                    'donor_id' => $isPledgingAnonymously ? null : Auth::id(),
                    'expire_date' => $expireDate,
                ]
            );
        } catch (ApiErrorException $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }

        $issue = GetIssueById::getWithActiveDonations($issueId);

        [$owner, $repo] = explode('/', $issue['repository']['title']);
        $issueNumber = basename(parse_url($issue['github_url'], PHP_URL_PATH));

        $installationId = $issue['repository']['githubInstallation']['installation_id'];

        $donorName = $isAuthenticated || !$isPledgingAnonymously ? Auth::user()->name : "Anonymous Pledger";
        $formattedExpireDate = $expireDate ? Carbon::parse($expireDate)->format('F j, Y') : null;
        $totalBounty = $issue->donations_sum_amount;
        $existingPledge = ($issue->donations_sum_amount - $amount) > 0;

        if ($existingPledge) {
            $comment = ConstructComment::constructShortPledgeComment($amount, $donorName, $issueId, $totalBounty, $formattedExpireDate);
        } else {
            $comment = ConstructComment::constructPledgeComment($amount, $donorName, $issueId, $formattedExpireDate);
        }

        // Query users who have this issue as an active issue (resolvers)
        $usersWithActiveIssue = User::whereHas('active_issues', function ($query) use ($issueId) {
            $query->where('issue_id', $issueId);
        })->get();

        GithubService::commentOnIssue($installationId, $owner, $repo, $issueNumber, $comment);
        SendNewPledgeMail::send($donorEmail, $donorName, $issueId, $amount, $usersWithActiveIssue);

        return new JsonResponse(['success' => true]);
    }

    public static function processDonations(Issue $issue, User $dbUser)
    {
        $today = Carbon::now()->toDateString();
        $donations = Donation::where('donatable_id', $issue->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', $today);
            })
            ->where('paid', false)
            ->get();

        if ($donations->isEmpty()) {
            logger('[INFO] No unpaid donations found for issue', ['issue_id' => $issue->id]);
            return;
        }

        $donationsSumAmount = $donations->sum('amount');
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
    }
}
