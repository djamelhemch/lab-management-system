{{-- resources/views/quotations/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            🧾 Quotation <span class="text-indigo-600">#{{ $quotation['id'] }}</span>
        </h1>
        <a href="{{ route('quotations.index') }}"
           class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
            ← Back to List
        </a>
    </div>

    {{-- Quotation Summary --}}
    <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-3">Summary</h2>
        <div class="grid md:grid-cols-2 gap-4 text-gray-700">
            <div>
                <strong>Patient:</strong>
                @if(!empty($quotation['patient']))
                    <a href="{{ route('patients.show', $quotation['patient_id']) }}"
                       class="text-blue-600 hover:underline font-medium">
                        {{ $quotation['patient']['full_name'] }}
                        @if(!empty($quotation['patient']['file_number']))
                            <span class="text-gray-500 text-sm">
                                ({{ $quotation['patient']['file_number'] }})
                            </span>
                        @endif
                    </a>
                @else
                    N/A
                @endif
            </div>

            <div>
                <strong>Status:</strong>
                <span class="capitalize px-2 py-1 rounded text-sm
                    {{ $quotation['status'] === 'draft' ? 'bg-yellow-100 text-yellow-700'
                       : ($quotation['status'] === 'validated' ? 'bg-green-100 text-green-700'
                       : 'bg-gray-100 text-gray-600') }}">
                    {{ $quotation['status'] }}
                </span>
            </div>

            <div>
                <strong>Total:</strong>
                <span class="font-semibold">{{ number_format($quotation['total'], 2) }} DA</span>
            </div>

            <div>
                <strong>Net Total:</strong>
                <span class="font-semibold">{{ number_format($quotation['net_total'], 2) }} DA</span>
            </div>

            <div>
                <strong>Remise:</strong>
                @if(!empty($quotation['agreement']))
                    {{ $quotation['agreement']['discount_value'] ?? 0 }}
                    @if(($quotation['agreement']['discount_type'] ?? '') === 'percentage') % @endif
                    ({{ number_format($quotation['discount_applied'] ?? 0, 2) }} DA)
                @else
                    0% (0 DA)
                @endif
            </div>

            <div>
                <strong>Created:</strong>
                {{ \Carbon\Carbon::parse($quotation['created_at'])->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    {{-- Analyses Table --}}
    <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-3">Analyses</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                        <th class="px-4 py-3 border">Name</th>
                        <th class="px-4 py-3 border">Code</th>
                        <th class="px-4 py-3 border">Created</th>
                        <th class="px-4 py-3 border text-right">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($quotation['analysis_items'] as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                {{ $item['analysis']['name'] ?? "Analysis #" . $item['analysis_id'] }}
                            </td>
                            <td class="px-4 py-2">{{ $item['analysis']['code'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                {{ !empty($item['analysis']['created_at'])
                                    ? \Carbon\Carbon::parse($item['analysis']['created_at'])->format('Y-m-d')
                                    : 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-right font-medium">
                                ${{ number_format($item['price'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500 italic">
                                No analyses found for this quotation.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

   {{-- Payment Summary --}}
@if(!empty($quotation['payments']))
<div class="bg-white rounded-2xl shadow-md p-6 mb-8">
    <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-3">Résumé de paiement</h2>

    {{-- Totals --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
        <div class="p-4 rounded-lg bg-green-50 text-green-700 text-center">
            <div class="text-sm">Total Facturé</div>
            <div class="text-lg font-bold">{{ number_format($quotation['net_total'], 2) }} DA</div>
        </div>

        <div class="p-4 rounded-lg {{ $quotation['outstanding'] > 0 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }} text-center">
            <div class="text-sm">Restant</div>
            <div class="text-lg font-bold">
                @if($quotation['outstanding'] > 0)
                    {{ number_format($quotation['outstanding'], 2) }} DA
                @else
                    Entièrement payé
                @endif
            </div>
        </div>

        <div class="p-4 rounded-lg bg-green-50 text-green-700 text-center">
            <div class="text-sm">Total Payé</div>
            <div class="text-lg font-bold">
                {{ number_format(collect($quotation['payments'])->sum('amount'), 2) }} DA
            </div>
        </div>
    </div>

    {{-- Individual Payments --}}
    <div class="space-y-6">
        @foreach($quotation['payments'] as $payment)
            <div class="p-6 rounded-xl bg-white border shadow-sm hover:shadow-md transition">
                
                {{-- Amount Paid --}}
                <div class="flex justify-between items-center text-lg font-semibold mb-3">
                    <span class="text-gray-700">Montant payé:</span>
                    <span class="text-green-600 text-xl font-bold">
                        {{ number_format($payment['amount'], 2) }} DA
                    </span>
                </div>

                {{-- Cash-specific fields --}}
                @if($payment['method'] === 'cash')
                    <div class="flex justify-between text-base mb-2">
                        <span class="text-gray-600">Montant Reçu:</span>
                        <span class="text-gray-900 font-medium">
                            {{ number_format($payment['amount_received'] ?? 0, 2) }} DA
                        </span>
                    </div>
                    <div class="flex justify-between text-base mb-2">
                        <span class="text-gray-600">Monnaie rendu:</span>
                        <span class="text-gray-900 font-medium">
                            {{ number_format($payment['change_given'] ?? 0, 2) }} DA
                        </span>
                    </div>
                @endif

                {{-- Encaisser par --}}
                <div class="flex justify-between text-base mb-2">
                    <span class="text-gray-600">Encaisser par:</span>
                    <span class="text-gray-900 font-medium">
                        {{ $payment['user']['full_name'] ?? 'N/A' }}
                    </span>
                </div>

                {{-- Method --}}
                <div class="flex justify-between text-base mb-2">
                    <span class="text-gray-600">Méthode de paiement:</span>
                    <span class="text-gray-900 font-medium">
                        {{ ucfirst($payment['method'] ?? 'N/A') }}
                    </span>
                </div>

                {{-- Paid At --}}
                <div class="flex justify-between text-base">
                    <span class="text-gray-600">Date d'encaissement:</span>
                    <span class="text-gray-900">
                        {{ \Carbon\Carbon::parse($payment['paid_at'])->format('Y-m-d H:i') }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

    {{-- Footer Actions --}}
    <div class="flex justify-end space-x-4 mt-6">
        {{-- Convert to Visit --}}
        <form action="{{ route('quotations.convert', $quotation['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition">
                Convert to Visit
            </button>
        </form>

        {{-- Download PDF --}}
        <a href="{{ route('quotations.download', $quotation['id']) }}"
           class="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
            Télécharger en PDF
        </a>
    </div>
</div>
@endsection
