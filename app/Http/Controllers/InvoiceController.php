<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexInvoiceRequest;
use App\Jobs\GenerateInvoiceNumberJob;
use App\Models\Invoice;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(IndexInvoiceRequest $request): Response
    {
        return Inertia::render('Invoices/Index', [
            'invoices' => Invoice::with('donation')->latest()->paginate(10)
        ]);
    }

    public function create(IndexInvoiceRequest $request): Response
    {
        // Get the incoming data (if any)
        $invoiceData = $request->old() ?: $request->all();

        return Inertia::render('Invoices/Create', [
            'invoice' => $invoiceData,
        ]);
    }

    public function store(IndexInvoiceRequest $request)
    {
        $invoiceData = $request->all();
        $total = $this->calculateTotal($invoiceData['items']);
        $vatValue = $this->getVatValue($total, $invoiceData['invoice']['vat']);
        $invoiceData['invoice']['vat_value'] = $vatValue;
        $invoiceData['invoice']['total'] = $total;
        $invoiceData['invoice']['total_vat'] = $total + $vatValue;
        $invoiceData['invoice']['status'] = 'NotPaid';
        dispatch(new GenerateInvoiceNumberJob($invoiceData))->onQueue('default');

        return redirect()->route('invoices.index');
    }

    private function calculateTotal($items) {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price_per_unit'] * $item['quantity'];
        }
        return $total;
    }

    private function getVatValue($total, $vat) {
        return $total * $vat / 100;
    }

    public function viewPdf($invoiceNumber)
    {
        // Check if the user has permission
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Get the path of the PDF from storage
        $pdfPath = "invoices/{$invoiceNumber}.pdf";

        // Check if the file exists in private storage
        if (!Storage::disk('private')->exists($pdfPath)) {
            abort(404, 'PDF not found.');
        }

        // Serve the file
        return response()->file(Storage::disk('private')->path($pdfPath));
    }
}
