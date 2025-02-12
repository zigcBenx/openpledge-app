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
        dispatch(new GenerateInvoiceNumberJob($event->donation));
    }
}
