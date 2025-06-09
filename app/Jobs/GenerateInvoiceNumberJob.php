<?php

namespace App\Jobs;

use App\Mail\PledgeInvoiceMail;
use App\Models\Donation;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceNumberJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $invoiceData;
    private int $donationId;
    
    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public $uniqueFor = 3600; // 1 hour
    
    /**
     * Create a new job instance.
     */
    public function __construct($invoiceData, $donationId = null)
    {
        $this->invoiceData = $invoiceData;
        $this->donationId = $donationId;
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId()
    {
        return $this->donationId ?: 'manual-invoice-' . md5(serialize($this->invoiceData));
    }

    /**
     * Execute the job.
     */
    public function handle(): Invoice
    {
        return DB::transaction(function () {
            // Check if invoice already exists for this donation
            if ($this->donationId) {
                $existingInvoice = Invoice::where('donation_id', $this->donationId)->first();
                if ($existingInvoice) {
                    logger()->info("Invoice already exists for donation: {$this->donationId}");
                    return $existingInvoice;
                }
                
                $this->invoiceData = $this->generateInvoiceData($this->donationId);
            }
            
            $invoiceNumber = $this->generateInvoiceNumber();
            $pdf = $this->generateInvoicePdf($invoiceNumber);
            $pdfPath = $this->storePdf($pdf, $invoiceNumber);
            $invoice = $this->saveInvoiceRecord($invoiceNumber, $pdfPath);
    //        $this->sendInvoiceMail($pdfPath, $invoice);

            logger()->info("Invoice PDF generated successfully: {$invoiceNumber}");
            return $invoice;
        });
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
            'tempDir' => storage_path('app/dompdf_temp_' . $this->donationId . '_' . uniqid()),
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

        if (!$invoice->donation) return;

        $donorEmail = $invoice->donation->user->email ?? null;

        if ($donorEmail) {
            Mail::to($donorEmail)->send(new PledgeInvoiceMail($invoice, $pdfPath));
            logger()->info("Invoice email sent to donor: {$donorEmail}");
        } else {
            logger()->warning("Donor email not found for donation ID: {$invoice->donation_id}");
        }
    }

    private function generateInvoiceData($donationId): array
    {
        $donation = Donation::with('user')->find($donationId);
        return [
            'customer' => [
                'name' => $donation->user->name,
                'email' => $donation->user->email,
            ],
            'items' => [
                [
                    'name'  => 'Pledge on OpenPledge.io',
                    'price_per_unit' => $donation->gross_amount,
                    'quantity' => 1,
                    'currency' => '€',
                ]
            ],
            'invoice' => [
                'invoice_date' => $donation->created_at,
                'payment_date' => $donation->created_at,
                'service_date' => $donation->created_at,
                'donation_id' => $donation->id,
                'vat' => 0,
                'vat_value' => 0,
                'total' => $donation->gross_amount,
                'total_vat' => $donation->gross_amount,
                'payment_method' => 'Stripe',
                'status' => 'Paid',
            ]
        ];
    }
}
