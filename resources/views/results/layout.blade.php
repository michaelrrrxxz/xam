@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $setting->school_name ?? 'Document' }}</title>
    <style>
        @page {
            /* margin-top: 25.4mm;     1 inch */
            /* margin-left: 38.1mm;    1.5 inch */
            margin-right: 12.7mm;   /* 0.5 inch */
            margin-left: 24.4mm;   /* 0.5 inch */
            margin-bottom: 12.7;  /* 1 inch */
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #28a745;
            padding-bottom: 6px;
            margin-bottom: 20px;
        }

        .header img {
            height: 100px;
            margin-right: 25px;
        }

        .header .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .header .title {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }

        .content {
            margin-top: 10px;
            min-height: 800px; /* leaves space for footer */
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
            border-top: 1px solid #28a745;
            padding-top: 5px;
            position: fixed;
            bottom: 15mm;
            left: 38.1mm;
            right: 12.7mm;
        }

        .doc-title {
            display: block;
            font-size: 14px;
            margin-top: 3px;
            font-weight: bold;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
            font-weight: bold;
            color: #28a745;
            opacity: 0.06;
            white-space: nowrap;
            pointer-events: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-size: 13px;
        }

        tr:nth-child(even) td {
            background: #f8f9fa;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header ">
            <div class="school-name">{{ $setting->school_name ?? 'School Name' }}</div>
        @if($setting && $setting->school_logo)
            <img src="{{ asset('storage/' . $setting->school_logo) }}" alt="School Logo">
        @endif
        <div class="title">@yield('title', 'Document Title')</div>
    </div>

    <!-- Watermark -->
    <div class="watermark">{{ $setting->school_name ?? 'CONFIDENTIAL' }}</div>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <span class="doc-title">@yield('title', 'Document Title')</span><br>
        &copy; {{ date('Y') }} {{ $setting->school_name ?? 'Your Institution' }} · Confidential Document
    </div>
</body>
</html>
