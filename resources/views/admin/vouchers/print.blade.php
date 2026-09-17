<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Vouchers - {{ $batch->batch_reference }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-btn-wrap {
            text-align: center;
            padding: 15px;
        }

        .print-btn {
            display: inline-block;
            padding: 12px 28px;
            background: #10B981;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .page {
            width: 194mm; /* A4(210mm) - 2x8mm margins */
            height: 277mm; /* A4(297mm) - 2x10mm margins */
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(6, 42.5mm);
            gap: 4mm;
            overflow: hidden;
            align-content: start;
        }

        /* Each voucher card — standard fixed dimensions (62mm x 42.5mm) */
        .voucher-card {
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
            height: 42.5mm;
            max-height: 42.5mm;
            min-height: 42.5mm;
            width: 100%;
        }

        /* Header band */
        .card-top {
            background: #000;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2mm 3mm;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }

        /* Package name */
        .pkg-label {
            background: #f0f0f0;
            border-bottom: 1px solid #999;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1mm 2mm;
            flex-shrink: 0;
        }

        /* Main body */
        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5mm 3mm;
            gap: 1mm;
        }

        .code-label {
            font-size: 6.5pt;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .voucher-code {
            border: 1.5px solid #000;
            padding: 1.5mm 3mm;
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 3.5px;
            text-align: center;
            width: 100%;
            background: #f8f8f8;
            font-family: monospace;
        }

        .price-line {
            font-size: 10pt;
            font-weight: 900;
            color: #000;
        }

        /* Footer band */
        .card-footer {
            background: #f0f0f0;
            border-top: 1px dashed #999;
            font-size: 6.5pt;
            color: #333;
            text-align: center;
            padding: 1mm 2mm;
            flex-shrink: 0;
        }

        /* Print media overrides */
        @media print {
            .print-btn-wrap { display: none; }

            .page {
                page-break-after: always;
                break-after: page;
            }
            .page:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }
        }
    </style>
</head>
<body>

<div class="print-btn-wrap">
    <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
</div>

@php
    $pkg = $batch->package;
    $tenantName = auth()->user() && auth()->user()->tenant ? auth()->user()->tenant->name : config('app.name', 'Wi-Fi Hotspot');
@endphp

@foreach($vouchers->chunk(18) as $pageIndex => $chunk)
<div class="page">
    @foreach($chunk as $voucher)
    <div class="voucher-card">
        <!-- Header -->
        <div class="card-top">
            <span>{{ $tenantName }}</span>
            <span>WiFi Hotspot</span>
        </div>

        <!-- Package Name -->
        <div class="pkg-label">
            {{ $pkg ? $pkg->name : 'Internet Voucher' }}
            @if($pkg && $pkg->human_duration)
                &bull; {{ $pkg->human_duration }}
            @endif
        </div>

        <!-- Body -->
        <div class="card-body">
            <div class="code-label">Vocha PIN</div>
            <div class="voucher-code">{{ $voucher->code }}</div>
            <div class="price-line">
                TSh {{ $pkg ? number_format($pkg->price) : '0' }}
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            Ingiza namba ya vocha kwenye ukurasa wa WiFi kuanza kutumia
        </div>
    </div>
    @endforeach
</div>
@endforeach

<script>
    window.onload = function () {
        setTimeout(function () { window.print(); }, 500);
    };
</script>

</body>
</html>
