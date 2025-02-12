<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateInvoiceNumberJob;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function generate(Request $request)
    {
        $invoiceData = [];
        $invoice = dispatch_sync(new GenerateInvoiceNumberJob($invoiceData));

        return response()->json([
            'invoice' => $invoice,
            'message' => 'Invoice generated successfully'
        ]);
    }
}
