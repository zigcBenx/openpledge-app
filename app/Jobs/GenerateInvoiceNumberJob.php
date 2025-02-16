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

    private $invoiceData;
    /**
     * Create a new job instance.
     */
    public function __construct($invoiceData)
    {
        $this->invoiceData = $invoiceData;
    }

    /**
     * Execute the job.
     */
    public function handle(): Invoice
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $pdf = $this->generateInvoicePdf($invoiceNumber);
        $pdfPath = $this->storePdf($pdf, $invoiceNumber);
        $invoice = $this->saveInvoiceRecord($invoiceNumber, $pdfPath);
        $this->sendInvoiceMail($pdfPath, $invoice);

        logger()->info("Invoice PDF generated successfully: {$invoiceNumber}");
        return $invoice;
    }

    private function generateInvoiceNumber(): string
    {
        return Invoice::generateInvoiceNumber($this->invoiceData['invoice'][Invoice::NUMBERING_DATE_COLUMN]);
    }

    private function generateInvoicePdf(string $invoiceNumber): \Barryvdh\DomPDF\PDF
    {
        $stampSrc = $this->getBase64DataForImage('images/openpledge_stamp.png');
        $logoSrc = $this->getBase64DataForImage('images/logotip_black.png');
        // we remove year from numbering, because of accounting requirements
        $invoiceNumberFormatted = explode('-', $invoiceNumber, 2)[1];
        return Pdf::loadView('invoices.invoice_pledge', [
            'invoice_number' => $invoiceNumberFormatted,
            'invoice_data' => $this->invoiceData,
            'stamp' => $stampSrc,
            'logo' => $logoSrc
        ])->setPaper('a4')->setOptions([
            'defaultFont' => 'dejavusans',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
    }

    private function getBase64DataForImage($imagePath): string
    {
        $path = public_path($imagePath);
        $imgData = base64_encode(file_get_contents($path));
        return 'data:image/png;base64,' . $imgData;
    }

    private function storePdf(\Barryvdh\DomPDF\PDF $pdf, string $invoiceNumber): string
    {
        $pdfPath = "invoices/{$invoiceNumber}.pdf";
        Storage::disk('private')->put($pdfPath, $pdf->output());
        return $pdfPath;
    }

    private function saveInvoiceRecord(string $invoiceNumber, string $pdfPath): Invoice
    {
        return Invoice::create([
            'number' => $invoiceNumber,
            'donation_id' => $this->invoiceData['invoice']['donation_id'] ?? null,
            'pdf_path' => $pdfPath,
            'customer' => $this->invoiceData['customer']['name'],
            'email' => $this->invoiceData['customer']['email'] ?? '',
            'invoice_date' => $this->invoiceData['invoice']['invoice_date'],
            'payment_date' => $this->invoiceData['invoice']['payment_date'],
            'service_date' => $this->invoiceData['invoice']['service_date'],
            'payment_method' => $this->invoiceData['invoice']['payment_method'],
            'vat' => $this->invoiceData['invoice']['vat'],
            'items' => json_encode($this->invoiceData['items']),
            'total' => $this->invoiceData['invoice']['total']
        ]);
    }

    private function sendInvoiceMail($pdfPath, $invoice): void
    {
        // Retrieve donor's email
        $invoice->load('donation.user');
        
        if (!$invoice->donation) return; // TODO: mkae optional from ui

        $donorEmail = $invoice->donation->user->email ?? null;

        if ($donorEmail) {
            Mail::to($donorEmail)->send(new PledgeInvoiceMail($invoice, $pdfPath));
            logger()->info("Invoice email sent to donor: {$donorEmail}");
        } else {
            logger()->warning("Donor email not found for donation ID: {$invoice->donation_id}");
        }
    }
}
