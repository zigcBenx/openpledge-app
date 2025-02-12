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
                // TODO: Add company if applicable
            ],
            'items' => [
                [
                    'name'  => 'Pledge on OpenPledge.io',
                    'price_per_unit' => $event->donation->amount,
                    'quantity' => 1,
                    'currency' => '€',
                    'item_total' => $event->donation->amount,
                ]
            ],
            'invoice' => [
                'invoice_date' => now()->format('d.m.Y'),
                'payment_date' => $event->donation->created_at->format('d.m.Y'),
                'service_date' => $event->donation->created_at->format('F Y'),
                'donation_id' => $event->donation->id,
                'vat' => 0,
                'vat_value' => 0,
                'total' => $event->donation->amount,
                'total_vat' => $event->donation->amount,
                'payment_method' => 'Online (Stripe)',
            ]
        ];
        dispatch(new GenerateInvoiceNumberJob($invoiceData));
    }
}
