<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'donation_id', 'pdf_path', 'customer', 'email', 'invoice_date', 'payment_date', 'service_date', 'payment_method', 'vat', 'items', 'total'];

    const NUMBERING_DATE_COLUMN = 'service_date';

    public static function generateInvoiceNumber($invoiceDate)
    {
        $year = Carbon::parse($invoiceDate)->year;
        $lastInvoice = self::whereYear(self::NUMBERING_DATE_COLUMN, $year)
                            ->orderBy('id', 'desc')
                            ->first();

        if($lastInvoice === null) {
            return "{$year}-1-1-1";
        }

        return self::incrementInvoiceNumber($lastInvoice->number);
    }

    private static function incrementInvoiceNumber(string $invoiceNumber): string
    {
        $parts = explode('-', $invoiceNumber);

        if (count($parts) !== 4) {
            throw new InvalidArgumentException("Invalid invoice number format.");
        }

        $parts[1] = (int) $parts[1] + 1;

        return implode('-', $parts);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}
