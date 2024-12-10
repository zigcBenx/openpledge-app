<?php

namespace App\Console\Commands;

use App\Actions\Email\SendConnectStripeMail;
use App\Actions\Email\SendRefundMail;
use App\Actions\Payment\TransferFunds;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Donation;
use Carbon\Carbon;

class CheckExpiredDonations extends Command
{
    protected $signature = 'donations:check-expired';

    public function handle()
    {
        logger('[INFO] Task started', [$this->signature]);
        $today = Carbon::today();

        $feePercentage = config('app.platform_fee_percentage');

        $expiredDonations = Donation::where('expire_date', '<', $today)
            ->where('paid', false)
            ->get();

        foreach ($expiredDonations as $donation) {
            $refundAmount = $donation->amount - $donation->amount * ($feePercentage / 100);

            $donor = User::find($donation->donor_id);
            $donorEmail = $donor->email;
            $donorName = $donor->name;
            $destinationStripeId = $donor->stripe_id;

            if (isset($destinationStripeId)) {
                $transferId = TransferFunds::transfer($destinationStripeId, $refundAmount);
                $donation->update([
                    'paid' => true,
                    'refund_transaction_id' => $transferId
                ]);

                SendRefundMail::send($donorEmail, $donorName, $refundAmount);
                logger('[INFO] Processed a refund for expired donation', ['donation_id' => $donation->id, 'refund_amount' => $refundAmount]);
            } else {
                SendConnectStripeMail::send($donorEmail, $donorName, $refundAmount, "Refund");
                logger('[WARNING] Cannot refund funds: User does not have a connected Stripe account.', ['user_id' => $donor->id]);
            }
        }
        logger('[INFO] Task ended', [$this->signature]);
    }
}
