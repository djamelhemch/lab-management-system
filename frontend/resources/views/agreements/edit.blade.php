{{-- resources/views/quotations/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8" x-data="quotationEditForm()">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            ✏️ Edit Quotation <span class="text-indigo-600">#{{ $quotation['id'] }}</span>
        </h1>
        <a href="{{ route('quotations.index') }}" 
           class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
            ← Back to List
        </a>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('quotations.update', $quotation['id']) }}">
        @csrf
        @method('PUT')

        {{-- Quotation Summary --}}
        <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-3">Summary</h2>
            <div class="grid md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <strong>Patient:</strong> 
                    <span class="font-medium">
                        {{ trim(($quotation['patient']['first_name'] ?? '') . ' ' . ($quotation['patient']['last_name'] ?? '')) }}
                    </span>
                    <input type="hidden" name="patient_id" value="{{ $quotation['patient_id'] }}">
                </div>
                <div>
                    <strong>Status:</strong> 
                    <select name="status" class="border p-1 rounded">
                        <option value="draft" @if($quotation['status'] == 'draft') selected @endif>Draft</option>
                        <option value="confirmed" @if($quotation['status'] == 'confirmed') selected @endif>Confirmed</option>
                        <option value="converted" @if($quotation['status'] == 'converted') selected @endif>Converted</option>
                    </select>
                </div>
                <div>
                    <strong>Total:</strong> 
                    <span class="font-semibold text-blue-600" x-text="total.toFixed(2) + ' DA'"></span>
                </div>
                <div>
                    <strong>Outstanding:</strong> 
                    <span class="font-semibold text-red-600" x-text="outstanding.toFixed(2) + ' DA'"></span>
                </div>
            </div>
        </div>

        {{-- Analyses --}}
        <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-3">Analyses</h2>

            {{-- Search & Add Analysis --}}
            <div class="mb-4 relative">
                <input 
                    type="text" 
                    x-model="analysisSearch"
                    @input="searchAnalyses"
                    @focus="showAnalysisSuggestions = true"
                    placeholder="Search analyses to add..."
                    class="w-full p-3 border border-gray-300 rounded"
                    autocomplete="off">
                <div x-show="showAnalysisSuggestions && analysisSuggestions.length > 0"
                     class="absolute z-10 w-full bg-white border border-gray-300 rounded-b-lg shadow-lg max-h-60 overflow-y-auto">
                    <template x-for="analysis in analysisSuggestions" :key="analysis.id">
                        <div @click="addAnalysis(analysis)"
                             class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                            <div class="font-medium" x-text="analysis.name"></div>
                            <div class="text-sm text-gray-600">
                                Price: <span x-text="analysis.price"></span> DA
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Selected Analyses Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <th class="px-4 py-3 border">Name</th>
                            <th class="px-4 py-3 border">Code</th>
                            <th class="px-4 py-3 border text-right">Price (DA)</th>
                            <th class="px-4 py-3 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <template x-for="(analysis, index) in selectedAnalyses" :key="analysis.id">
                            <tr>
                                <td class="px-4 py-2" x-text="analysis.name"></td>
                                <td class="px-4 py-2" x-text="analysis.code ?? 'N/A'"></td>
                                <td class="px-4 py-2 text-right" x-text="Number(analysis.price).toFixed(2)"></td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" @click="removeAnalysis(index)" 
                                            class="text-red-600 hover:text-red-800">
                                        Remove
                                    </button>
                                </td>

                                {{-- Hidden Inputs for Form Submission --}}
                                <input type="hidden" :name="'analyses[' + index + '][analysis_id]'" :value="analysis.id">
                                <input type="hidden" :name="'analyses[' + index + '][price]'" :value="analysis.price">
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payments --}}
        <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-3">Payments</h2>

            {{-- Existing Payments (readonly) --}}
            @foreach($quotation['payments'] as $payment)
            <div class="p-4 rounded-lg bg-gray-50 border mb-4 grid grid-cols-2 gap-4">
                <div>
                    <span>Montant Facturé:</span>
                    <input type="number" value="{{ $payment['amount'] }}" readonly class="border p-1 rounded w-full bg-gray-100">
                </div>
                <div>
                    <span>Montant Reçu:</span>
                    <input type="number" value="{{ $payment['amount_received'] ?? 0 }}" readonly class="border p-1 rounded w-full bg-gray-100">
                </div>
                <div>
                    <span>Méthode:</span>
                    <input type="text" value="{{ ucfirst($payment['method'] ?? 'N/A') }}" readonly class="border p-1 rounded w-full bg-gray-100">
                </div>
            </div>
            @endforeach

            {{-- New Payment (optional, updates outstanding) --}}
            <div class="p-4 rounded-lg bg-green-50 border">
                <h3 class="font-medium mb-3">Add Payment</h3>
                <div class="flex justify-between mb-2">
                    <span>Amount:</span>
                    <input type="number" step="0.01" x-model="newPayment.amount" name="new_payment[amount]" class="border p-1 rounded w-28 text-right">
                </div>
                <div class="flex justify-between mb-2">
                    <span>Method:</span>
                    <select x-model="newPayment.method" name="new_payment[method]" class="border p-1 rounded">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <textarea x-model="newPayment.notes" name="new_payment[notes]" placeholder="Notes..." class="w-full border p-2 rounded"></textarea>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end space-x-4 mt-6">
            <button type="submit" 
                class="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
                💾 Update Quotation
            </button>
            <a href="{{ route('quotations.show', $quotation['id']) }}" 
               class="px-5 py-2 rounded-lg bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function quotationEditForm() {
    return {
        selectedAnalyses: @json($quotation['analysis_items']).map(item => ({
            id: item.analysis.id,
            name: item.analysis.name,
            code: item.analysis.code,
            price: item.price
        })),
        analysisSearch: '',
        analysisSuggestions: [],
        showAnalysisSuggestions: false,
        total: {{ $quotation['total'] ?? 0 }},
        outstanding: {{ $quotation['outstanding'] ?? 0 }},
        newPayment: { amount: 0, method: 'cash', notes: '' },

        async searchAnalyses() {
            if (this.analysisSearch.length < 2) { this.analysisSuggestions = []; return; }
            const response = await fetch(`/quotations/search-analyses?q=${encodeURIComponent(this.analysisSearch)}`);
            this.analysisSuggestions = await response.json();
        },

        addAnalysis(analysis) {
            if (!this.selectedAnalyses.find(a => a.id === analysis.id)) {
                this.selectedAnalyses.push({...analysis});
                this.calculateTotal();
            }
            this.analysisSearch = '';
            this.showAnalysisSuggestions = false;
        },

        removeAnalysis(index) {
            this.selectedAnalyses.splice(index, 1);
            this.calculateTotal();
        },

        calculateTotal() {
            this.total = this.selectedAnalyses.reduce((sum, a) => sum + parseFloat(a.price || 0), 0);
            this.outstanding = this.total - (this.selectedAnalyses.reduce((s, p) => s + (p.amount_received ?? 0), 0) + this.newPayment.amount);
        }
    }
}
</script>
@endsection
