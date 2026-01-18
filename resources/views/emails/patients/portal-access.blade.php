<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }

        .credentials-box {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .credential-row {
            display: flex;
            margin: 10px 0;
        }

        .credential-label {
            font-weight: bold;
            width: 100px;
        }

        .credential-value {
            font-family: monospace;
            background: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }

        .button {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }

        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        @if($settings->logo_path)
            @php
                $logoPath = storage_path('app/public/' . $settings->logo_path);
            @endphp
            @if(file_exists($logoPath))
                <img src="{{ $message->embed($logoPath) }}" alt="Logo"
                    style="max-height: 80px; width: auto; margin-bottom: 10px;">
            @endif
        @endif
        <h1 style="margin: 0; color: #1f2937;">{{ $settings->business_name }}</h1>
        <p style="color: #6b7280; margin: 5px 0;">Patient Portal Access</p>
    </div>

    <p>Dear <strong>{{ $patient->name }}</strong>,</p>

    <p>Your patient portal account has been created. You can now access your medical records,
        view prescriptions, and track your health history online.</p>

    <div class="credentials-box">
        <h3 style="margin-top: 0;">Your Login Credentials</h3>
        <p><strong>Email:</strong> <span class="credential-value">{{ $patient->email }}</span></p>
        <p><strong>Temporary Password:</strong> <span class="credential-value">{{ $password }}</span></p>
    </div>

    <div class="warning">
        <strong>⚠️ Important:</strong> For your security, please change your password after your first login.
    </div>

    <div style="text-align: center;">
        <a href="{{ $loginUrl }}" class="button">Login to Patient Portal</a>
    </div>

    <p>If you have any questions or need assistance, please contact us at:</p>
    <ul>
        @if($settings->phone)
            <li>Phone: {{ $settings->phone }}</li>
        @endif
        @if($settings->email)
            <li>Email: {{ $settings->email }}</li>
        @endif
    </ul>

    <div class="footer">
        <p>This is an automated message from {{ $settings->business_name }}.</p>
        <p>If you did not request this account, please contact us immediately.</p>
        <p>Software powered by UviTech, Inc.</p>
    </div>
</body>

</html>