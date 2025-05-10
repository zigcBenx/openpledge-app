<?php

namespace App\Actions\PendingDonation;

class CreateNewPendingDonation
{
    public static function create($donations, $resolver)
    {
        foreach ($donations as $donation) {
            $donation->pendingDonations()->firstOrCreate(
                [ // one donation should never be inserted twice in pending donations
                    'donation_id'      => $donation->id
                ],
                [
                    'amount'           => $donation->net_amount,
                    'user_github_name' => $resolver,
                ]
            );
        }
    }
}
