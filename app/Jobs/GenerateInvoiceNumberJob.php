<?php

namespace App\Jobs;

use App\Mail\PledgeInvoiceMail;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $donation;
    /**
     * Create a new job instance.
     */
    public function __construct($donation)
    {
        $this->donation = $donation;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $invoiceNumber = $this->generateInvoiceNumber();
            $pdf = $this->generateInvoicePdf($invoiceNumber);
            $pdfPath = $this->storePdf($pdf, $invoiceNumber);
            $invoice = $this->saveInvoiceRecord($invoiceNumber, $pdfPath);
            $this->sendInvoiceMail($pdfPath, $invoice);

            logger()->info("Invoice PDF generated successfully: {$invoiceNumber}");
        } catch (\Exception $e) {
            logger()->error("Failed to generate PDF invoice: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }

    private function generateInvoiceNumber(): string
    {
        return Invoice::generateInvoiceNumber();
    }

    private function generateInvoicePdf(string $invoiceNumber): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('invoices.invoice_pledge', [
            'invoice_number' => $invoiceNumber,
            'donation' => $this->donation,
        ])->setPaper('a4')->setOptions([
            'defaultFont' => 'dejavusans',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
    }

    private function storePdf(\Barryvdh\DomPDF\PDF $pdf, string $invoiceNumber): string
    {
        $pdfPath = "invoices/{$invoiceNumber}.pdf";
        Storage::put($pdfPath, $pdf->output());
        return $pdfPath;
    }

    private function saveInvoiceRecord(string $invoiceNumber, string $pdfPath): Invoice
    {
        return Invoice::create([
            'number' => $invoiceNumber,
            'donation_id' => $this->donation->id,
            'pdf_path' => $pdfPath,
        ]);
    }

    private function sendInvoiceMail($pdfPath, $invoice): void
    {
        // Retrieve donor's email
        $invoice->load('donation.user');
        $donorEmail = $invoice->donation->user->email ?? null;

        if ($donorEmail) {
            Mail::to($donorEmail)->send(new PledgeInvoiceMail($invoice, $pdfPath));
            logger()->info("Invoice email sent to donor: {$donorEmail}");
        } else {
            logger()->warning("Donor email not found for donation ID: {$invoice->donation_id}");
        }
    }
}
