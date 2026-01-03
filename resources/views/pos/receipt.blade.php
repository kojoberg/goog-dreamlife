<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->status === 'pending_payment' ? 'Invoice' : 'Receipt' }} #{{ $sale->id }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 14px;
            color: #000000;
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
            border-collapse: collapse;
        }

        td {
            padding: 4px 0;
            color: #000;
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
    <!-- Debug: Items: {{ $sale->items->count() }}, Tax: {{ $sale->tax_amount }} -->


    <div class="text-center">
        @if($settings->logo_path)
            <?php 
                                                                $logoPath = storage_path('app/public/' . $settings->logo_path); 
                                                            ?>
            @if(file_exists($logoPath))
                <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                    alt="Logo" style="max-width: 100%; max-height: 120px; height: auto; width: auto; margin-bottom: 15px;">
            @endif
        @endif
        <h2 style="margin:0;">{{ $settings->business_name }}</h2>
        @if($settings->tin_number)
            <p style="margin:2px 0; font-size:12px;">TIN: {{ $settings->tin_number }}</p>
        @endif
        @if($settings->address)
            <p style="margin:5px 0;">{{ $settings->address }}</p>
        @endif
        @if($settings->phone)
            <p>Tel: {{ $settings->phone }}</p>
        @endif
        @if($settings->email)
            <p>Email: {{ $settings->email }}</p>
        @endif
        @if(auth()->check() && auth()->user()->branch)
            <p class="bold" style="margin-top:5px;">Branch: {{ auth()->user()->branch->name }}</p>
        @endif
    </div>

    <div class="divider"></div>

    <div>
        <table style="font-size: 12px;">
            <tr>
                <td>Date:</td>
                <td class="right">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>{{ $sale->status === 'pending_payment' ? 'Invoice' : 'Receipt' }} #:</td>
                <td class="right">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td>Prescribed/Served By:</td>
                <td class="right">{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
            </tr>
            @if($sale->shift && $sale->shift->user)
                <tr>
                    <td>Cashier:</td>
                    <td class="right">{{ $sale->shift->user->name }}</td>
                </tr>
            @endif
            @if($sale->patient)
                <tr>
                    <td>Customer:</td>
                    <td class="right">{{ $sale->patient->name }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th style="text-align: left;">Item</th>
                <th class="right">Qty</th>
                <th class="right">Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($sale->items && $sale->items->count() > 0)
                @foreach($sale->items as $item)
                    <tr>
                        <td colspan="4" style="padding-bottom: 0; font-weight: bold; color: #000;">
                            {{ $item->product ? $item->product->name : 'Unknown Item' }}
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding-top: 0;"></td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="right">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center">No items found.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>

        @if(!empty($sale->tax_breakdown) && is_array($sale->tax_breakdown))
            @foreach($sale->tax_breakdown as $taxName => $taxValue)
                @if($taxValue > 0)
                    <tr>
                        <td style="padding-left: 10px; font-size: 12px; color: #000;">{{ strtoupper($taxName) }}</td>
                        <td class="right" style="font-size: 12px; color: #000;">{{ number_format($taxValue, 2) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td style="font-weight: bold;">Total Tax</td>
                <td class="right" style="font-weight: bold;">{{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
        @elseif($sale->tax_amount > 0)
            <tr>
                <td>Tax</td>
                <td class="right">{{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
        @endif

        <!-- Loyalty Discount -->
        @if($sale->discount_amount > 0)
            <tr>
                <td>Discount / Loyalty</td>
                <td class="right">-{{ number_format($sale->discount_amount, 2) }}</td>
            </tr>
        @endif

        <tr class="bold" style="font-size: 16px;">
            <td>Total</td>
            <td class="right">{{ $settings->currency_symbol }} {{ number_format($sale->total_amount, 2) }}</td>
        </tr>

        @if($sale->status === 'completed' && $sale->amount_tendered > 0)
            <tr>
                <td>Tendered ({{ ucfirst($sale->payment_method) }})</td>
                <td class="right">{{ number_format($sale->amount_tendered, 2) }}</td>
            </tr>
            <tr>
                <td>Change</td>
                <td class="right">{{ number_format($sale->change_amount, 2) }}</td>
            </tr>
        @endif
    </table>

    @if($sale->status === 'pending_payment')
        <div class="text-center bold" style="margin: 10px 0; border: 2px solid #000; padding: 5px;">
            PAYMENT PENDING
        </div>
        <div class="text-center" style="font-size: 12px;">
            Please pay at the Cashier.
        </div>
    @endif

    @if($sale->patient)
        <div style="font-size: 12px; margin-top: 10px; text-align: center;">
            Loyalty Points Earned: {{ $sale->points_earned }} <br>
            Current Balance: {{ $sale->patient->loyalty_points }}
        </div>
    @endif

    <div class="footer-print" style="margin-top: 20px; text-align: center;">
        <p>Thank you for your patronage!</p>
        <p>Software powered by UviTech, Inc.</p>
    </div>

    <div class="text-center no-print" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print
            Receipt</button>
        <br><br>

        @auth
            @if($sale->status === 'completed' && !$sale->refund)
                <div x-data="{ open: false }" style="margin-bottom: 20px;">
                    <button @click="open = true"
                        style="color: red; text-decoration: underline; background: none; border: none; cursor: pointer; font-size: 14px;">
                        Request Refund
                    </button>

                    <!-- Refund Modal (Simple JS/CSS for standalone page) -->
                    <div x-show="open"
                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
                        <div
                            style="background: white; width: 90%; max-width: 400px; margin: 100px auto; padding: 20px; border-radius: 8px; text-align: left;">
                            <h3 style="margin-top: 0;">Request Refund</h3>
                            <p>Please provide a reason for this refund request.</p>

                            <form action="{{ route('refunds.store', $sale) }}" method="POST">
                                @csrf
                                <textarea name="reason" rows="3" required
                                    style="width: 100%; padding: 8px; margin-bottom: 10px;"
                                    placeholder="Reason (e.g., Wrong item, customer changed mind)"></textarea>

                                <div style="text-align: right;">
                                    <button type="button" @click="open = false"
                                        style="padding: 8px 16px; margin-right: 10px; cursor: pointer;">Cancel</button>
                                    <button type="submit"
                                        style="padding: 8px 16px; background: red; color: white; border: none; border-radius: 4px; cursor: pointer;">Submit
                                        Request</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Load Alpine.js for this page if not present in layout (Receipt is standalone) -->
                <script src="//unpkg.com/alpinejs" defer></script>
            @elseif($sale->refund)
                <p style="color: orange; font-style: italic;">Refund Status: {{ ucfirst($sale->refund->status) }}</p>
            @endif
        @endauth

        @if(auth()->check() && auth()->user()->isCashier())
            <a href="{{ route('cashier.index') }}">Back to Dashboard</a>
        @else
            <a href="{{ route('pos.index') }}">Back to POS</a>
        @endif
    </div>
</body>



</html>