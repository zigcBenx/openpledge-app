<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice_number }}</title>
    <style>
         body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 14px; color: #333; }
         .invoice-container {
            width: 100%;
            display: flex;
            justify-content: center; /* Centers the invoice horizontally */
            align-items: center;
            padding: 30px 0;
        }

        .invoice-box {
            width: 90%;
            max-width: 700px; /* Limits width for a balanced look */
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); /* Light shadow for a better look */
        }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 120px; }
        .company-details { text-align: center; font-size: 12px; color: #555; margin-bottom: 20px; }
        .invoice-title { font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 10px; }
        .invoice-details, .donation-details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .invoice-details td, .donation-details td { padding: 8px; border: 1px solid #ddd; }
        .total { font-size: 16px; font-weight: bold; text-align: right; }
        .footer { text-align: center; font-size: 12px; margin-top: 20px; color: #777; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-box">
            
            <!-- Company Header -->
            <div class="header">
                <img src="https://app.openpledge.io/images/logo.png" alt="OpenPledge Logo">
            </div>
            <div class="company-details">
                <strong>Open Pledge j.d.o.o.</strong> <br>
                Radnička cesta 20, Zagreb 10000, Croatia <br>
                Email: ziga@openpledge.io
            </div>

            <!-- Invoice Title -->
            <div class="invoice-title">INVOICE</div>

            <!-- Invoice Details -->
            <table class="invoice-details">
                <tr>
                    <td><strong>Invoice #:</strong> {{ $invoice_number }}</td>
                    <td><strong>Date:</strong> {{ now()->format('Y-m-d') }}</td>
                </tr>
            </table>

            <!-- Donation Details -->
            <table class="donation-details">
                <tr>
                    <td><strong>Donor ID:</strong></td>
                    <td>{{ $donation->donor_id ?? 'Anonymous' }}</td>
                </tr>
                <tr>
                    <td><strong>Transaction ID:</strong></td>
                    <td>{{ $donation->transaction_id }}</td>
                </tr>
                <tr>
                    <td><strong>Amount Donated:</strong></td>
                    <td>€{{ number_format($donation->amount, 2) }}</td>
                </tr>
            </table>

            <!-- Total -->
            <div class="total">
                Total Amount: <strong>€{{ number_format($donation->amount, 2) }}</strong>
            </div>

            <!-- Footer -->
            <div class="footer">
                Thank you for your generous contribution! <br>
                This invoice is auto-generated and serves as a receipt of your donation.
            </div>
        </div>
    </div>
</body>
</html>
