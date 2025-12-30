<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Report #{{ $shift->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .summary {
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .metric {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            text-align: left;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" style="margin-bottom: 20px; padding: 10px;">Print Report</button>

    <div class="header">
        <h2>Shift Report</h2>
        <p>{{ $shift->user->name }}</p>
        <p>{{ $shift->start_time->format('d M Y H:i') }} -
            {{ $shift->end_time ? $shift->end_time->format('H:i') : 'OPEN' }}</p>
    </div>

    <div class="summary">
        <div class="metric">
            <span>Starting Cash:</span>
            <span>${{ number_format($shift->starting_cash, 2) }}</span>
        </div>
        <div class="metric">
            <span>Total Sales (Cash):</span>
            <span>${{ number_format($shift->sales->where('payment_method', 'cash')->sum('total_amount'), 2) }}</span>
        </div>
        <div class="metric">
            <span>Total Sales (Card):</span>
            <span>${{ number_format($shift->sales->where('payment_method', 'card')->sum('total_amount'), 2) }}</span>
        </div>
        <div class="metric">
            <span>Ending Cash (Recorded):</span>
            <span>${{ number_format($shift->actual_cash, 2) }}</span>
        </div>
    </div>

    <h3>Sales Log</h3>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Ref</th>
                <th>Method</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shift->sales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('H:i') }}</td>
                    <td>{{ $sale->reference_number }}</td>
                    <td>{{ ucfirst($sale->payment_method) }}</td>
                    <td>${{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Grand Total: ${{ number_format($shift->sales->sum('total_amount'), 2) }}
    </div>

</body>

</html>