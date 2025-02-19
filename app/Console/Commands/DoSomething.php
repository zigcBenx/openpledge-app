<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoiceNumberJob;
use App\Models\Donation;
use Illuminate\Console\Command;

class DoSomething extends Command
{
    protected $signature = 'do:something';

    public function handle()
    {
        $donations = Donation::with('user')->get();

        foreach($donations as $donation) {
            $this->info('Processing donation: ' . $donation->id);
            $invoiceData = [
                'customer' => [
                    'name' => $donation->user->name,
                    'email' => $donation->user->email,
                    // TODO: Add company if applicable
                ],
                'items' => [
                    [
                        'name'  => 'Pledge on OpenPledge.io',
                        'price_per_unit' => $donation->amount,
                        'quantity' => 1,
                        'currency' => '€',
                    ]
                ],
                'invoice' => [
                    'invoice_date' => now(),
                    'payment_date' => $donation->created_at,
                    'service_date' => $donation->created_at,
                    'donation_id' => $donation->id,
                    'vat' => 0,
                    'vat_value' => 0,
                    'total' => $donation->amount,
                    'total_vat' => $donation->amount,
                    'payment_method' => 'Stripe',
                    'status' => 'Paid',
                ]
            ];
            dispatch(new GenerateInvoiceNumberJob($invoiceData))->onQueue('default');
        }
    }
}
