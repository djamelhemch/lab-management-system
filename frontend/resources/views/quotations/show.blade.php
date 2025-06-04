{{-- resources/views/quotations/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Quotation #{{ $quotation['id'] }}</h1>
        <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="mb-2"><strong>Patient:</strong> {{ $quotation['patient']['full_name'] ?? 'N/A' }}</div>
        <div class="mb-2"><strong>Status:</strong> <span class="capitalize">{{ $quotation['status'] }}</span></div>
        <div class="mb-2"><strong>Total:</strong> ${{ number_format($quotation['total'], 2) }}</div>
        <div class="mb-2"><strong>Created:</strong> {{ \Carbon\Carbon::parse($quotation['created_at'])->format('Y-m-d H:i') }}</div>
    </div>

    <div class="bg-white rounded shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Analyses</h2>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Analysis</th>
                    <th class="px-4 py-2">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation['quotation_items'] as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item['analysis']['name'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2">${{ number_format($item['price'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection