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
            max-width: 400px;
            margin: 0 auto;
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

        .metric.total {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }

        .metric.variance {
            font-weight: bold;
            padding: 5px;
            margin-top: 5px;
        }

        .variance-positive {
            background-color: #d4edda;
            color: #155724;
        }

        .variance-negative {
            background-color: #f8d7da;
            color: #721c24;
        }

        .variance-zero {
            background-color: #e8e8e8;
            color: #333;
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

        th {
            background-color: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .grand-total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
            font-size: 1.1em;
            padding: 10px;
            background-color: #f5f5f5;
        }

        .role-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            background-color: #e0e0e0;
        }

        .no-sales {
            text-align: center;
            padding: 20px;
            color: #666;
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
        <p><strong>{{ $shift->user->name }}</strong></p>
        <p><span class="role-badge">{{ ucfirst($shift->user->role) }}</span></p>
        <p>{{ $shift->start_time->format('d M Y H:i') }} -
            {{ $shift->end_time ? $shift->end_time->format('H:i') : 'OPEN' }}
        </p>
    </div>

    @php
        $isCashierShift = $shift->user->role === 'cashier';
        $cashTotal = $shiftSales->where('payment_method', 'cash')->sum('total_amount') ?? 0;
        $cardTotal = $shiftSales->where('payment_method', 'card')->sum('total_amount') ?? 0;
        $momoTotal = $shiftSales->where('payment_method', 'mobile_money')->sum('total_amount') ?? 0;
        $grandTotal = $shiftSales->sum('total_amount') ?? 0;
        $transactionCount = $shiftSales->count();
        $expectedCash = ($shift->starting_cash ?? 0) + $cashTotal;
        $variance = ($shift->actual_cash ?? 0) - $expectedCash;
        
        // For pharmacist shifts: check if any sales were processed directly (no cashier)
        // If all sales have cashier_shift_id, pharmacist didn't handle cash
        $directSalesCount = 0;
        $cashierProcessedCount = 0;
        if (!$isCashierShift) {
            $directSalesCount = $shiftSales->whereNull('cashier_shift_id')->count();
            $cashierProcessedCount = $shiftSales->whereNotNull('cashier_shift_id')->count();
        }
        
        // Pharmacist handles cash if they have direct sales (no cashier workflow)
        $pharmacistHandlesCash = !$isCashierShift && $directSalesCount > 0;
    @endphp

    <div class="summary">
        @if($isCashierShift || $pharmacistHandlesCash)
            {{-- Cashier Shift OR Pharmacist with direct sales: Show full cash handling summary --}}
            <h4 style="margin: 0 0 10px 0;">Cash Summary</h4>
            <div class="metric">
                <span>Starting Cash:</span>
                <span>₵{{ number_format($shift->starting_cash ?? 0, 2) }}</span>
            </div>
            <div class="metric">
                <span>Cash Sales:</span>
                <span>₵{{ number_format($cashTotal, 2) }}</span>
            </div>
            <div class="metric">
                <span>Expected Cash:</span>
                <span>₵{{ number_format($expectedCash, 2) }}</span>
            </div>
            @if($shift->end_time)
                <div class="metric">
                    <span>Actual Cash (Counted):</span>
                    <span>₵{{ number_format($shift->actual_cash ?? 0, 2) }}</span>
                </div>
                <div
                    class="metric variance {{ $variance > 0 ? 'variance-positive' : ($variance < 0 ? 'variance-negative' : 'variance-zero') }}">
                    <span>Variance:</span>
                    <span>{{ $variance > 0 ? '+' : '' }}₵{{ number_format($variance, 2) }}</span>
                </div>
            @endif
        @else
            {{-- Pharmacist Shift with all cashier-processed sales: Show invoice summary --}}
            <h4 style="margin: 0 0 10px 0;">Invoices Summary</h4>
            <div class="metric">
                <span>Invoices Generated:</span>
                <span>{{ $transactionCount }}</span>
            </div>
            <div class="metric">
                <span>Total Invoice Value:</span>
                <span>₵{{ number_format($grandTotal, 2) }}</span>
            </div>
            @if($cashierProcessedCount > 0)
                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                    <em>{{ $cashierProcessedCount }} invoice(s) processed by cashier. Cash handling is managed in cashier shift reports.</em>
                </p>
            @endif
        @endif
    </div>

    <div class="summary">
        <h4 style="margin: 0 0 10px 0;">Sales by Payment Method</h4>
        <div class="metric">
            <span>Cash:</span>
            <span>₵{{ number_format($cashTotal, 2) }}</span>
        </div>
        <div class="metric">
            <span>Card:</span>
            <span>₵{{ number_format($cardTotal, 2) }}</span>
        </div>
        <div class="metric">
            <span>Mobile Money:</span>
            <span>₵{{ number_format($momoTotal, 2) }}</span>
        </div>
        <div class="metric total">
            <span>Total Sales:</span>
            <span>₵{{ number_format($grandTotal, 2) }}</span>
        </div>
        <div class="metric" style="margin-top: 5px;">
            <span>Transactions:</span>
            <span>{{ $transactionCount }}</span>
        </div>
    </div>

    <h3>Sales Log ({{ $transactionCount }} transactions)</h3>
    @if($transactionCount > 0)
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Receipt #</th>
                    <th>Method</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shiftSales as $sale)
                    <tr>
                        <td>{{ $sale->created_at->format('H:i') }}</td>
                        <td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ ucfirst($sale->payment_method ?? 'N/A') }}</td>
                        <td class="text-right">₵{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grand-total">
            Grand Total: ₵{{ number_format($grandTotal, 2) }}
        </div>
    @else
        <div class="no-sales">
            No transactions recorded for this shift.
        </div>
    @endif

</body>

</html>