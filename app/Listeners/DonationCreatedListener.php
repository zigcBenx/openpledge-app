<?php

namespace App\Listeners;

use App\Events\DonationCreatedEvent;
use App\Jobs\GenerateInvoiceNumberJob;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class DonationCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DonationCreatedEvent $event): void
    {
        $invoiceData = [
            'customer' => [
                'name' => $event->donation->user->name,
                'email' => $event->donation->user->email,
                'company' => $event->donation->user->company,
                'should_bill_company' => isset($event->donation->company_id),
            ],
            'items' => [
                [
                    'name'  => 'Pledge on OpenPledge.io',
                    'price_per_unit' => $event->donation->gross_amount,
                    'quantity' => 1,
                    'currency' => '€',
                ]
            ],
            'invoice' => [
                'invoice_date' => now(),
                'payment_date' => $event->donation->created_at,
                'service_date' => $event->donation->created_at,
                'donation_id' => $event->donation->id,
                'vat' => 0,
                'vat_value' => 0,
                'total' => $event->donation->gross_amount,
                'total_vat' => $event->donation->gross_amount,
                'payment_method' => 'Stripe',
                'status' => 'Paid',
            ]
        ];
        dispatch(new GenerateInvoiceNumberJob($invoiceData, $event->donation->id))->onQueue('default');
    }
}
