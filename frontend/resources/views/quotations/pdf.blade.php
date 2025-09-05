<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; color: #333; }
        h1, h2, h3 { margin: 0 0 10px; }
        h1 { font-size: 20px; color: #bc1622; }
        h3 { margin-top: 20px; font-size: 14px; }

        .header { border-bottom: 2px solid #bc1622; padding-bottom: 10px; margin-bottom: 20px; }
        .meta { margin-bottom: 20px; }
        .meta p { margin: 3px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; font-size: 12px; }
        td { font-size: 12px; }

        .totals { margin-top: 20px; width: 100%; }
        .totals td { border: none; padding: 4px; }
        .totals .label { text-align: right; font-weight: bold; }
        .totals .value { text-align: right; }

    .payment-box {
            border: 1px solid #ccc;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 12px;
            background-color: #f9f9f9;
        }

        .payment-box h4 {
            margin: 0 0 8px;
            font-size: 14px;
            color: #1a202c;
            font-weight: bold;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .payment-box .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .payment-box .row strong {
            font-weight: 700;
            color: #4a5568;
        }

        .footer { margin-top: 40px; font-size: 11px; text-align: center; color: #666; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <img src="{{ public_path('images/logo_lab.png') }}" alt="Lab Logo" style="height:60px;">
        </div>
        <div style="text-align:right;">
            <h1>Facture #{{ $quotation['id'] }}</h1>
            <p style="margin:0; font-size:12px; color:#666;">Abdelatif Lab</p>
        </div>
    </div>

    {{-- Meta Information --}}
    <div class="meta">
        <p><strong>Patient:</strong> {{ $quotation['patient']['full_name'] ?? 'N/A' }}</p>
        <p><strong>Dossier N°:</strong>
            @if(!empty($quotation['patient']['file_number']))
                <span style="color: #666;">{{ $quotation['patient']['file_number'] }}</span>
            @endif
        </p>
        <p><strong>Status:</strong> {{ ucfirst($quotation['status']) }}</p>
        <p><strong>Date de facturation:</strong> {{ \Carbon\Carbon::parse($quotation['created_at'])->format('Y-m-d H:i') }}</p>
    </div>

    {{-- Analyses --}}
    <h3>Analyses</h3>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Code</th>
                <th>Prix (DA)</th>
            </tr>
        </thead>
        <tbody>
        @forelse($quotation['analysis_items'] as $item)
            <tr>
                <td>{{ $item['analysis']['name'] ?? 'N/A' }}</td>
                <td>{{ $item['analysis']['code'] ?? 'N/A' }}</td>
                <td>{{ number_format($item['price'], 2) }} DA</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align:center; color:#999;">No analyses found</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals">
        <tr>
            <td class="label">Total:</td>
            <td class="value">{{ number_format($quotation['total'], 2) }} DA</td>
        </tr>
        <tr>
            <td class="label">Remise:</td>
            <td class="value">
                @if(!empty($quotation['agreement']))
                    {{ $quotation['agreement']['discount_value'] }}
                    {{ $quotation['agreement']['discount_type'] === 'percentage' ? '%' : ' DA' }}
                    ({{ number_format($quotation['discount_applied'] ?? 0, 2) }} DA)
                @else
                    0%
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Net Total:</td>
            <td class="value"><strong>{{ number_format($quotation['net_total'], 2) }} DA</strong></td>
        </tr>
        <tr>
            <td class="label">Restant:</td>
            <td class="value">
                @if(($quotation['outstanding'] ?? 0) > 0)
                    {{ number_format($quotation['outstanding'], 2) }} DA
                @else
                    <span style="color: green; font-weight: bold;">Entièrement payer</span>
                @endif
            </td>
        </tr>
    </table>

    @if(!empty($quotation['payments']))
    <h3>Résumé de paiement</h3>
    @foreach($quotation['payments'] as $payment)
        <div class="payment-box">
            <h4>Payment #{{ $payment['id'] }}</h4>
            <div class="row"><strong>Methode de payment:</strong> <span>{{ ucfirst($payment['method'] ?? 'N/A') }}</span></div>
            <div class="row"><strong>Montant a payer:</strong> <span>{{ number_format($payment['amount'], 2) }} DA</span></div>
            @if($payment['method'] === 'cash')
                <div class="row"><strong>Montant Reçu:</strong> <span>{{ number_format($payment['amount_received'] ?? 0, 2) }} DA</span></div>
                <div class="row"><strong>Monnaie Rendu:</strong> <span>{{ number_format($payment['change_given'] ?? 0, 2) }} DA</span></div>
            @endif
            <div class="row"><strong>Encaisser par:</strong> <span>{{ $payment['user']['full_name'] ?? 'N/A' }}</span></div>
            <div class="row"><strong>Payer Le:</strong> <span>{{ \Carbon\Carbon::parse($payment['paid_at'])->format('Y-m-d H:i') }}</span></div>
        </div>
    @endforeach
    @endif

    {{-- Footer --}}
    <div class="footer">
        Abdelatif Lab — Facture Pro Format
    </div>
</body>
</html>
