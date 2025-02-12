<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 14px; color: #333; }
        .invoice-container { width: 100%; display: flex; justify-content: center; padding: 30px 0; }
        .invoice-box { width: 90%; max-width: 700px; padding: 20px; border: 1px solid #ddd; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); position: relative; }
        .header { text-align: left; display: flex; justify-content: space-between; align-items: center; }
        .header img { max-width: 140px; }
        .company-details { font-size: 12px; color: #555; }
        .invoice-title { font-size: 24px; font-weight: bold; text-align: right; }
        .invoice-details, .donation-details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .invoice-details td, .donation-details td { padding: 8px; border: 1px solid #ddd; }
        .invoice-details td:first-child, .donation-details td:first-child { font-weight: bold; }
        .total { font-size: 16px; font-weight: bold; text-align: right; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; margin-top: 20px; color: #777; }
        .stamp { position: absolute; bottom: 20px; right: 20px; max-width: 120px; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-box">
            
            <!-- Header -->
            <div class="header">
                <img src="https://app.openpledge.io/images/logo.png" alt="OpenPledge Logo">
                <div class="invoice-title">INVOICE</div>
            </div>

            <!-- Company Details -->
            <div class="company-details">
                <strong>Open Pledge j.d.o.o.</strong><br>
                Radnička cesta 20, Zagreb 10000, Croatia<br>
            </div>

            <!-- Invoice Details -->
            <table class="invoice-details">
                <tr>
                    <td>Invoice #:</td>
                    <td>{{ $invoice_number }}</td>
                    <td>Invoice Date:</td>
                    <td>{{ now()->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td>Payment Method:</td>
                    <td>Online (Stripe)</td>
                    <td>Payment Date:</td>
                    <td>{{ $donation->created_at->format('Y-m-d') }}</td>
                </tr>
            </table>

            <!-- Invoice Items -->
            <table class="donation-details">
                <tr>
                    <th>ITEM</th>
                    <th>Quantity</th>
                    <th>Price/Unit</th>
                    <th>Total (without VAT)</th>
                </tr>
                <tr>
                    <td>Donation to OpenPledge</td>
                    <td>1</td>
                    <td>€{{ number_format($donation->amount, 2) }}</td>
                    <td>€{{ number_format($donation->amount, 2) }}</td>
                </tr>
            </table>

            <!-- Total Section -->
            <div class="total">
                Total (without VAT): €{{ number_format($donation->amount, 2) }}<br>
                0% VAT: €0.00<br>
                <strong>Invoice TOTAL: €{{ number_format($donation->amount, 2) }}</strong>
            </div>

            <div>
                <p>Invoice created by:</p>
                <p>Neja Gozdnikar Benko</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                Thank you for supporting open source development!<br>
                This document serves as an official receipt for your donation.
            </div>
            <hr style="color:#87f5dd; margin-top:100px;">
            <div class="footer">
                OPEN PLEDGE j.d.o.o, registered at Commercial Court in Zagreb <br>
                Erste&Steiermärkische Bank d.d., IBAN: HR5424020061101195436 <br>
                VAT Number: HR09487318276, Registration number (MBS): 081519410
            </div>
        </div>
    </div>
</body>
</html>
