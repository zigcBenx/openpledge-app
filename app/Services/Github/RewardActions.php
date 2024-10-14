<?php

namespace App\Services\Github;

use App\Actions\Email\SendIssueResolverMail;
use App\Models\Issue;
use App\Models\User;
use App\Models\Donation;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Transfer;

class RewardActions
{
    public static function processDonations(Issue $issue, User $dbUser)
    {
        $today = Carbon::now()->toDateString();
        $donationsSumAmount = Donation::where('donatable_id', $issue->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', $today);
            })
            ->where('paid', false)
            ->sum('amount');

        $destinationStripeId = $dbUser->stripe_id;

        if (isset($destinationStripeId)) {
            self::transferFunds($destinationStripeId, $donationsSumAmount, $issue->id);
        } else {
            logger('[WARNING] Cannot transfer funds: User does not have a connected Stripe account.', ['user_id' => $dbUser->id]);
            // TODO: Send email to notify the user to connect Stripe account to OpenPledge
        }

        $resolverMail = $dbUser->email;
        $issueId = $issue->id;

        // Query users who have this issue as an active issue but exclude the current resolver
        $usersWithActiveIssue = User::whereHas('active_issues', function ($query) use ($issueId) {
            $query->where('issue_id', $issueId);
        })->where('email', '!=', $resolverMail)->get();

        SendIssueResolverMail::send($resolverMail, $dbUser->name, $issue->id, $usersWithActiveIssue); // Send emails to all resolvers during beta, regardless of Stripe connection
    }

    private static function transferFunds(string $destinationStripeId, $amount, int $issueId)
    {
        if ($amount <= 0) {
            return;
        }

        Stripe::setApiKey(config('app.stripe_secret'));

        try {
            $transfer = Transfer::create([
                'amount' => $amount * 100, // Multiplied by 100 because Stripe expects the amount in cents
                'currency' => 'eur',
                'destination' => $destinationStripeId
            ]);

            logger('[INFO] Funds transferred', ['issue_id' => $issueId, 'amount' => $amount, 'stripe_transfer_id' => $transfer->id]);

            Donation::where('donatable_id', $issueId)->update(['paid' => true]);
        } catch (\Exception $e) {
            logger('[ERROR] Stripe Transfer failed', ['error' => $e->getMessage(), 'issue_id' => $issueId]);
            throw $e;
        }
    }
}