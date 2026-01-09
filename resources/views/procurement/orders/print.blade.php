<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $order->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .details {
            margin-bottom: 20px;
            width: 100%;
        }

        .details td {
            padding: 5px;
            vertical-align: top;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.items th,
        table.items td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table.items th {
            background-color: #f4f4f4;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #666;
        }

        .total-row {
            font-weight: bold;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.history.back()">Back</button>
    </div>

    <div class="header">
        <h1>Purchase Order</h1>
        <h3>PO #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h3>
    </div>

    <table class="details">
        <tr>
            <td width="50%">
                <strong>From:</strong><br>
                @if($settings->logo_path)
                    <?php    $logoPath = storage_path('app/public/' . $settings->logo_path); ?>
                    @if(file_exists($logoPath))
                        <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                            alt="Logo" style="max-width: 150px; height: auto; margin-bottom: 10px;"><br>
                    @endif
                @endif
                <span style="font-size: 1.2em; font-weight: bold;">{{ $settings->business_name }}</span><br>
                {{ $settings->address }}<br>
                Tel: {{ $settings->phone }}<br>
                Email: {{ $settings->email }}<br>
                @if($settings->tin_number)
                    TIN: {{ $settings->tin_number }}
                @endif
            </td>
            <td width="50%" style="text-align: right;">
                <strong>To (Supplier):</strong><br>
                <div
                    style="border: 1px solid #ddd; padding: 10px; display: inline-block; text-align: left; min-width: 200px;">
                    <strong>{{ $order->supplier->name }}</strong><br>
                    Attn: {{ $order->supplier->contact_person }}<br>
                    {{ $order->supplier->phone }}<br>
                    {{ $order->supplier->address }}
                </div>
            </td>
        </tr>
    </table>

    <table class="details">
        <tr>
            <td>
                <strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d') }}<br>
                <strong>Expected Date:</strong> {{ $order->expected_date->format('Y-m-d') }}<br>
                <strong>Ordered By:</strong> {{ $order->user->name }}
            </td>
            <td>
                @if($order->status === 'received')
                    <strong>Status:</strong> RECEIVED<br>
                    <strong>Received By:</strong> {{ $order->received_by }}<br>
                    <strong>Date Received:</strong> {{ $order->updated_at->format('Y-m-d') }}
                @else
                    <strong>Status:</strong> PENDING
                @endif
            </td>
        </tr>
    </table>

    @if($order->notes)
        <div style="margin-bottom: 20px; border: 1px solid #eee; padding: 10px; background: #fafafa;">
            <strong>Notes:</strong><br>
            {{ $order->notes }}
        </div>
    @endif

    <table class="items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Batch #</th>
                <th>Expiry</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity_ordered }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ $item->batch_number ?? '-' }}</td>
                    <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ number_format($item->quantity_ordered * $item->unit_cost, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="total-row">Grand Total</td>
                <td style="font-weight: bold;">{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Software powered by UviTech, Inc.</p>
    </div>
</body>

</html>