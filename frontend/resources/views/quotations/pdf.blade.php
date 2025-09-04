<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation['id'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Quotation #{{ $quotation['id'] }}</h1>
    <p><strong>Patient:</strong> {{ $quotation['patient']['full_name'] ?? 'N/A' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($quotation['status']) }}</p>
    <p><strong>Total:</strong> ${{ number_format($quotation['total'], 2) }}</p>

    <h3>Analyses</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
        @foreach($quotation['analysis_items'] as $item)
            <tr>
                <td>{{ $item['analysis']['name'] ?? 'N/A' }}</td>
                <td>{{ $item['analysis']['code'] ?? 'N/A' }}</td>
                <td>${{ number_format($item['price'], 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
