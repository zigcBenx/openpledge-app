<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PledgeInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfPath;

    public function __construct($invoice, $pdfPath)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        logger('TOLE:');
        logger($this->pdfPath);
        return $this->subject('Your Donation Invoice')
                    ->with([
                        'invoice' => $this->invoice
                    ])
                    ->view('emails.pledge-invoice')
                    ->attach(storage_path("app/public/{$this->pdfPath}"));
    }
}