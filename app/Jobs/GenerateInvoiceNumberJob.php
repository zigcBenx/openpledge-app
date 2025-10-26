<?php

namespace App\Jobs;

use App\Mail\PledgeInvoiceMail;
use App\Models\Donation;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Symfony\Component\Process\Process;

class GenerateInvoiceNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $invoiceData;
    private ?int $donationId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $invoiceData, ?int $donationId = null)
    {
        $this->invoiceData = $invoiceData;
        $this->donationId = $donationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): Invoice
    {
        if (!$this->invoiceData) {
            $this->invoiceData = $this->generateInvoiceData($this->donationId);
        }

        $invoiceNumber = $this->generateInvoiceNumber();
        $pdfPath = $this->generateInvoicePdf($invoiceNumber);
        $invoice = $this->saveInvoiceRecord($invoiceNumber, $pdfPath);
        $this->sendInvoiceMail($pdfPath, $invoice);

        logger()->info("Invoice PDF generated successfully: {$invoiceNumber}");
        return $invoice;
    }

    private function generateInvoiceNumber(): string
    {
        return Invoice::generateInvoiceNumber($this->invoiceData['invoice'][Invoice::NUMBERING_DATE_COLUMN]);
    }

    private function generateInvoicePdf(string $invoiceNumber): string
    {
        $stampSrc = $this->getBase64DataForImage('images/openpledge_stamp.png');
        $logoSrc = $this->getBase64DataForImage('images/logotip_black.png');
        $invoiceNumberFormatted = explode('-', $invoiceNumber, 2)[1];
        $pdfPath = storage_path("app/private/invoices/{$invoiceNumber}.pdf");
        $htmlPath = storage_path("app/private/invoices/{$invoiceNumber}.html");

        $html = View::make('invoices.invoice_pledge', [
            'invoice_number' => $invoiceNumberFormatted,
            'invoice_data' => $this->invoiceData,
            'stamp' => $stampSrc,
            'logo' => $logoSrc
        ])->render();

        file_put_contents($htmlPath, $html);

        $process = new Process([
            'weasyprint',
            $htmlPath,
            $pdfPath
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("WeasyPrint failed: " . $process->getErrorOutput());
        }

        $this->cleanupHTMLfile($htmlPath);

        return "invoices/{$invoiceNumber}.pdf";
    }

    private function cleanupHTMLfile($htmlPath): void
    {
        unlink($htmlPath);
    }

    private function getBase64DataForImage(string $imagePath): string
    {
        $path = public_path($imagePath);
        $imgData = base64_encode(file_get_contents($path));
        return 'data:image/png;base64,' . $imgData;
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

    private function sendInvoiceMail(string $pdfPath, Invoice $invoice): void
    {
        $invoice->load('donation.user');

        if (!$invoice->donation) {
            return;
        }

        $donorEmail = $invoice->donation->user->email ?? null;

        if ($donorEmail) {
            Mail::to($donorEmail)->send(new PledgeInvoiceMail($invoice, $pdfPath));
            logger()->info("Invoice email sent to donor: {$donorEmail}");
        } else {
            logger()->warning("Donor email not found for donation ID: {$invoice->donation_id}");
        }
    }

    private function generateInvoiceData(int $donationId): array
    {
        $donation = Donation::with('user')->find($donationId);
        return [
            'customer' => [
                'name' => $donation->user->name,
                'email' => $donation->user->email,
            ],
            'items' => [
                [
                    'name' => 'Pledge on OpenPledge.io',
                    'price_per_unit' => $donation->gross_amount,
                    'quantity' => 1,
                    'currency' => '$',
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
