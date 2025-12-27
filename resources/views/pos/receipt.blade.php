<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 14px;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
        }

        td {
            padding: 4px 0;
        }

        .right {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>


    <div class="text-center">
        <h2 style="margin:0;">{{ $settings->business_name }}</h2>
        @if($settings->address)
            <p style="margin:5px 0;">{{ $settings->address }}</p>
        @endif
        @if($settings->phone)
            <p>Tel: {{ $settings->phone }}</p>
        @endif
        @if($settings->email)
            <p>Email: {{ $settings->email }}</p>
        @endif
    </div>

    <div class="divider"></div>

    <div>
        Date: {{ $sale->created_at->format('Y-m-d H:i') }}<br>
        Receipt #: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>
        Cashier: {{ $sale->user->name }}
    </div>

    <div class="divider"></div>

    <table>
        @foreach($sale->items as $item)
            <tr>
                <td colspan="2" class="bold">{{ $item->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        @if($sale->tax_breakdown)
            <tr>
                <td>Subtotal</td>
                <td class="right">GHS {{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>NHIL (2.5%)</td>
                <td class="right">{{ number_format($sale->tax_breakdown['nhil'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>GETFund (2.5%)</td>
                <td class="right">{{ number_format($sale->tax_breakdown['getfund'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>COVID-19 (1%)</td>
                <td class="right">{{ number_format($sale->tax_breakdown['covid'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>VAT (15%)</td>
                <td class="right">{{ number_format($sale->tax_breakdown['vat'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="bold">TOTAL PAYABLE</td>
                <td class="right bold">GHS {{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        @else
            <tr>
                <td class="bold">TOTAL</td>
                <td class="right bold">GHS {{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td>Payment Method</td>
            <td class="right">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
        </tr>
    </table>

    <div class="text-center" style="margin-top: 20px;">
        <p>Thank you for your patronage!</p>
        <p>Software by UviTech Ghana</p>
    </div>

    <div class="text-center no-print" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">Print Receipt</button>
        <br><br>
        <a href="{{ route('pos.index') }}">Back to POS</a>
    </div>
</body>

</html>