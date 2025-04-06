<?php

namespace App\Actions\PendingDonation;

class CreateNewPendingDonation
{
    public static function create($donations, $resolver)
    {
        foreach ($donations as $donation) {
            $donation->pendingDonations()->create([
                'amount'         => $donation->net_amount,
                'user_github_name' => $resolver,
            ]);
        }
    }
}
