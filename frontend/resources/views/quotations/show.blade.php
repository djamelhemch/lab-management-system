{{-- resources/views/quotations/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Quotation #{{ $quotation['id'] }}</h1>
        <a href="{{ route('quotations.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Back
        </a>
    </div>

    {{-- Quotation Summary --}}
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="mb-2">
            <strong>Patient:</strong>
            {{ $quotation['patient']['full_name'] ?? 'N/A' }}
            @if(!empty($quotation['patient']['file_number']))
                <span class="text-gray-500">({{ $quotation['patient']['file_number'] }})</span>
            @endif
        </div>

        <div class="mb-2"><strong>Status:</strong> 
            <span class="capitalize">{{ $quotation['status'] }}</span>
        </div>

        <div class="mb-2"><strong>Total:</strong> 
            ${{ number_format($quotation['total'], 2) }}
        </div>

        <div class="mb-2"><strong>Net Total:</strong> 
            ${{ number_format($quotation['net_total'], 2) }}
        </div>

        <div class="mb-2"><strong>Discount:</strong> 
            {{ $quotation['discount_applied'] ?? 0 }}%
        </div>

        <div class="mb-2"><strong>Created:</strong> 
            {{ \Carbon\Carbon::parse($quotation['created_at'])->format('Y-m-d H:i') }}
        </div>
    </div>

    {{-- Analyses Table --}}
    <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Analyses</h2>
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Code</th>
                    <th class="px-4 py-2 border">Created</th>
                    <th class="px-4 py-2 border">Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quotation['analysis_items'] as $item)
                    <tr>
                        <td class="px-4 py-2 border">
                            {{ $item['analysis']['name'] ?? "Analysis #" . $item['analysis_id'] }}
                        </td>
                        <td class="px-4 py-2 border">
                            {{ $item['analysis']['code'] ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 border">
                            {{ !empty($item['analysis']['created_at']) 
                                ? \Carbon\Carbon::parse($item['analysis']['created_at'])->format('Y-m-d') 
                                : 'N/A' }}
                        </td>
                        <td class="px-4 py-2 border">
                            ${{ number_format($item['price'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">
                            No analyses found for this quotation.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payment Summary --}}
    @if(!empty($quotation['payments']))
    <div class="bg-gray-50 rounded shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Payment Summary</h2>

        {{-- Totals --}}
        <div class="flex justify-between font-medium mb-3 border-b pb-2">
            <span>Total Paid:</span>
            <span class="text-green-600">${{ number_format($quotation['total_paid'], 2) }}</span>
        </div>
        <div class="flex justify-between font-medium mb-4">
            <span>Outstanding:</span>
            <span class="text-red-600">
                @if($quotation['outstanding'] > 0)
                    ${{ number_format($quotation['outstanding'], 2) }}
                @else
                    <span class="text-green-600 font-semibold">Fully Paid</span>
                @endif
            </span>
        </div>

        {{-- Individual Payments --}}
        @foreach($quotation['payments'] as $payment)
        <div class="mb-2 border-b pb-2">
            <div class="flex justify-between">
                <span>Method:</span>
                <span>{{ ucfirst($payment['method'] ?? 'N/A') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Amount Paid:</span>
                <span class="text-green-600">${{ number_format($payment['amount'], 2) }}</span>
            </div>
            @if($payment['method'] === 'cash')
            <div class="flex justify-between">
                <span>Amount Received:</span>
                <span>${{ number_format($payment['amount_received'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Change Given:</span>
                <span>${{ number_format($payment['change_given'] ?? 0, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm">
                <span>Cashier:</span>
                <span>{{ $payment['user']['full_name'] ?? 'N/A' }}</span> 
            </div>
            <div class="flex justify-between">
                <span>Paid At:</span>
                <span>{{ \Carbon\Carbon::parse($payment['paid_at'])->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Footer Actions --}}
    <div class="flex justify-end space-x-3">
        {{-- Convert to Visit --}}
        <form action="{{ route('quotations.convert', $quotation['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Convert to Visit
            </button>
        </form>

        {{-- Download PDF --}}
        <a href="{{ route('quotations.download', $quotation['id']) }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Download PDF
        </a>
    </div>
</div>
@endsection
