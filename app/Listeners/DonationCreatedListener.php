<?php

namespace App\Listeners;

use App\Events\DonationCreatedEvent;
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
        $donation = $event->donation;

        // Generate invoice number
        $invoiceNumber = Invoice::generateInvoiceNumber();
        $pdfPath = "invoices/{$invoiceNumber}.pdf";

        // Generate PDF from view
        $pdf = Pdf::loadView('invoices.invoice_pledge', [
            'invoice_number' => $invoiceNumber,
            'donation' => $donation,
        ])->setPaper('a4')->setOptions([
            'defaultFont' => 'dejavusans',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // Save PDF to storage (public folder)
        Storage::put("$pdfPath", $pdf->output());

        Invoice::create([
            'number' => $invoiceNumber,
            'donation_id' => $donation->id,
            'pdf_path' => $pdfPath,
        ]);

        logger('Donation listener triggered and PDF invoice created.');
    }
}
