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
        @if($settings->logo_path)
            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo"
                style="max-width: 100%; max-height: 80px; height: auto; width: auto; margin-bottom: 10px;">
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
    </div>

    <!-- ... (rest of receipt) ... -->

    <div class="text-center" style="margin-top: 20px;">
        <p>Thank you for your patronage!</p>
        <p>Software powered by UviTech, Inc.</p>
    </div>

    <div class="text-center no-print" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">Print Receipt</button>
        <br><br>
        <a href="{{ route('pos.index') }}">Back to POS</a>
    </div>
</body>

</html>