<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: monospace;
        }

        .container {
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
        }

        .text-center {
            text-align: center;
        }

        .right {
            text-align: right;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="text-center">
            @if($settings->logo_path)
                <img src="{{ $message->embed(storage_path('app/public/' . $settings->logo_path)) }}" alt="Logo"
                    style="max-height: 120px; width: auto; margin-bottom: 15px;">
            @endif
            <h2 style="margin:0;">{{ $settings->business_name }}</h2>
            @if($settings->address)
            <p style="margin:5px 0;">{{ $settings->address }}</p> @endif
            @if($settings->phone)
            <p>Tel: {{ $settings->phone }}</p> @endif
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
                @foreach($sale->tax_breakdown as $taxName => $taxAmount)
                    <tr>
                        <td>{{ strtoupper($taxName) }}</td>
                        <td class="right">{{ number_format($taxAmount, 2) }}</td>
                    </tr>
                @endforeach
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
            @if($sale->patient)
                <tr>
                    <td colspan="2" class="divider"></td>
                </tr>
                <tr>
                    <td>Points Earned</td>
                    <td class="right">{{ $sale->points_earned ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Total Points Balance</td>
                    <td class="right">{{ $sale->patient->loyalty_points }}</td>
                </tr>
            @endif
        </table>

        <div class="text-center" style="margin-top: 20px;">
            <p>Thank you for your patronage!</p>
            <p>Software powered by UviTech, Inc.</p>
        </div>
    </div>
</body>

</html>