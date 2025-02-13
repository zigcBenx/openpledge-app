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
        .invoice-title { font-size: 20px; font-weight: bold; }
        .invoice-details, .donation-details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .invoice-details td, .donation-details td { padding: 2px; border: 1px solid #ddd; }
        .invoice-details td:first-child, .donation-details td:first-child { font-weight: bold; }
        .total { font-size: 16px; font-weight: bold; text-align: right; margin-top: 10px; }
        .additional-ino { font-size: 12px; margin-top: 20px; color: #777; }
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
            </div>

            <!-- Company Details -->
            <!-- <div class="company-details">
                <strong>Open Pledge j.d.o.o.</strong><br>
                Radnička cesta 20, Zagreb 10000, Croatia<br>
            </div> -->

            <!-- Invoice Details -->
             <table style="width: 100%; margin-bottom: 50px;">
                <tr>
                    <td style="width: 50%">
                        <br>
                        <p>{!! nl2br(e($invoice_data['customer']['name'])) !!}</p>
                        @if(isset($invoice_data['customer']['email']))
                            <p>{{ $invoice_data['customer']['email'] }}</p>
                        @endif
                    </td>
                    <td style="width: 50%">
                        <table style="width: 100%">
                            <tr>
                                <td><div class="invoice-title">INVOICE</div></td>
                                <td>{{ $invoice_number }}</td>
                            </tr>
                            <tr>
                                <td>Invoice Date:</td>
                                <td>{{ \Carbon\Carbon::parse($invoice_data['invoice']['invoice_date'])->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td>Payment Date:</td>
                                <td>{{ \Carbon\Carbon::parse($invoice_data['invoice']['payment_date'])->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td>Service Date:</td>
                                <td>{{ \Carbon\Carbon::parse($invoice_data['invoice']['service_date'])->format('F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Payment Method:</td>
                                <td>{{ $invoice_data['invoice']['payment_method'] }}</td>
                            </tr>
                        </table>
                    </td>
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
                @foreach($invoice_data['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['currency'] }}{{ number_format($item['price_per_unit'], 2) }}</td>
                    <td>{{ $item['currency'] }}{{ number_format($item['price_per_unit'], 2) * $item['quantity'] }}</td>
                </tr>
                @endforeach
            </table>

            <!-- Total Section -->
            <div class="total">
                Total (without VAT): {{ $item['currency'] }}{{ number_format($invoice_data['invoice']['total'], 2) }}<br>
                {{ $invoice_data['invoice']['vat'] }}% VAT: {{ $item['currency'] }}{{ $invoice_data['invoice']['vat_value'] }}<br>
                <strong>Invoice TOTAL: {{ $item['currency'] }}{{ number_format($invoice_data['invoice']['total_vat'], 2) }}</strong>
            </div>

            <div class="additional-ino">
                @if($invoice_data['invoice']['status'] !== "Paid")
                    <p>When paying, please refer to the invoice number: {{ $invoice_number }}</p>
                @endif
                <p>
                    Payment information - IBAN: HR54 24020061101195436 (Erste&Steiermärkische Bank d.d.)<br>
                    VAT: Oslobođeno PDV a prema čl. 40. st. 1. t. b. Zakona o PDV u
                </p>
            </div>

            <div>
                <p>Invoice created by:</p>
                <p>Neja Gozdnikar Benko</p>
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
