<?php

namespace App\Actions\Payment;
use App\Actions\Donation\CreateNewDonation;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;
use App\Actions\Issue\GetIssueById;
use App\Actions\Comment\ConstructComment;
use App\Actions\Email\SendNewPledgeMail;
use Stripe\Exception\ApiErrorException;
use App\Services\Github\GitHubService;

class ProcessPayment
{
    public static function process($pledgeExpirationDate, $pledgeExpirationYear, $paymentId, $issueId, $amount, $donorEmail): JsonResponse
    {
        $expireDate = null;
        if ($pledgeExpirationDate && $pledgeExpirationYear) {
            // Extract day and month from 'DD/MM' format & combine with year to form 'Y-m-d' format
            [$day, $month] = explode('/', $pledgeExpirationDate['value']);
            $expireDate = date('Y-m-d', strtotime("{$pledgeExpirationYear}-$month-$day"));
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
                    'donor_id' => Auth::id(),
                    'expire_date' => $expireDate,
                ]
            );
        } catch (ApiErrorException $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }

        $issue = GetIssueById::get($issueId);

        [$owner, $repo] = explode('/', $issue['repository']['title']);
        $issueNumber = basename(parse_url($issue['github_url'], PHP_URL_PATH));

        $installationId = $issue['repository']['githubInstallation']['installation_id'];

        $donorName = Auth::user()->name;
        $comment = ConstructComment::constructPledgeComment($amount, $donorName, $issueId);

        // Query users who have this issue as an active issue (resolvers)
        $usersWithActiveIssue = User::whereHas('active_issues', function ($query) use ($issueId) {
            $query->where('issue_id', $issueId);
        })->get();

        GithubService::commentOnIssue($installationId, $owner, $repo, $issueNumber, $comment);
        SendNewPledgeMail::send($donorEmail, $donorName, $issueId, $amount, $usersWithActiveIssue);

        return new JsonResponse(['success' => true]);
    }
}
