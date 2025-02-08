<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'donation_id', 'pdf_path'];

    public static function generateInvoiceNumber()
    {
        $year = now()->year;
        $lastInvoice = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->number, -4)) + 1 : 1;
        return sprintf('INV-%d-%04d', $year, $nextNumber);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}
