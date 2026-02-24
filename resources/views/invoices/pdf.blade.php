<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; margin: 24px; }
        h1, h2, h3, p { margin: 0; }
        .row { width: 100%; margin-bottom: 16px; }
        .left { float: left; width: 55%; }
        .right { float: right; width: 45%; text-align: right; }
        .clear { clear: both; }
        .muted { color: #6b7280; }
        .card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .grid th, .grid td { border: 1px solid #e5e7eb; padding: 8px; }
        .grid th { background: #f9fafb; text-align: left; }
        .num { text-align: right; }
        .totals { width: 45%; margin-left: auto; margin-top: 12px; border-collapse: collapse; }
        .totals td { padding: 6px 8px; border: 1px solid #e5e7eb; }
        .totals .label { background: #f9fafb; }
        .totals .grand { font-weight: 700; }
        .footer { margin-top: 20px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="row">
        <div class="left">
            <h1>INVOICE</h1>
            <p class="muted">{{ $invoice->invoice_number }}</p>
        </div>
        <div class="right">
            <p><strong>Issued:</strong> {{ optional($invoice->issued_at)->format('d M Y H:i') }}</p>
            <p><strong>Due:</strong> {{ optional($invoice->due_at)->format('d M Y') ?? '-' }}</p>
            <p><strong>Status:</strong> {{ ucfirst((string) $invoice->status?->value ?? (string) $invoice->status) }}</p>
            <p><strong>Version:</strong> {{ $invoice->version }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="row card">
        <div class="left">
            <h3>Bill To</h3>
            <p>{{ data_get($invoice->organization_snapshot, 'name', 'Personal registration') }}</p>
            <p class="muted">Booker: {{ data_get($invoice->booker_snapshot, 'name', '-') }}</p>
            <p class="muted">{{ data_get($invoice->booker_snapshot, 'email', '-') }}</p>
        </div>
        <div class="right">
            <h3>Event</h3>
            <p>{{ data_get($invoice->event_snapshot, 'title', '-') }}</p>
            <p class="muted">Date: {{ data_get($invoice->event_snapshot, 'date', '-') }}</p>
            <p class="muted">Type: {{ ucfirst((string) data_get($invoice->event_snapshot, 'type', '-')) }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th style="width: 40%;">Package</th>
                <th style="width: 15%;" class="num">Qty</th>
                <th style="width: 20%;" class="num">Unit Price</th>
                <th style="width: 25%;" class="num">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->package_name }}</td>
                    <td class="num">{{ $item->participant_count }}</td>
                    <td class="num">IDR {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="num">IDR {{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subtotal</td>
            <td class="num">IDR {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Discount</td>
            <td class="num">IDR {{ number_format((float) $invoice->discount_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Tax</td>
            <td class="num">IDR {{ number_format((float) $invoice->tax_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label grand">Grand Total</td>
            <td class="num grand">IDR {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($invoice->notes)
        <div class="row" style="margin-top: 14px;">
            <h3>Notes</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif

    @if($invoice->void_reason)
        <div class="row" style="margin-top: 10px;">
            <h3>Void Reason</h3>
            <p>{{ $invoice->void_reason }}</p>
        </div>
    @endif

    <div class="footer">
        Generated at {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>
