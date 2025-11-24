@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8"
     x-data="quotationEditForm({{ json_encode($quotation) }}, {{ json_encode($analyses ?? []) }})"
     x-init="init()">

    {{-- Floating Header Card --}}
    <div class="container mx-auto px-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#bc1622] rounded-2xl flex items-center justify-center shadow-sm">
                        <span class="text-white text-xl">✏️</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Quotation</h1>
                        <p class="text-sm text-gray-500">Reference #{{ $quotation['id'] }}</p>
                    </div>
                </div>
                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl bg-white text-gray-600 font-medium 
                          border border-gray-200 hover:bg-gray-100 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitForm" class="container mx-auto px-4">
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

            {{-- Main Content Area --}}
            <div class="xl:col-span-3 space-y-6">

                {{-- Patient Info Banner --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-lg text-gray-900">
                            {{ trim(($quotation['patient']['first_name'] ?? '') . ' ' . ($quotation['patient']['last_name'] ?? '')) }}
                        </h2>
                        <p class="text-sm text-gray-500">Patient Information</p>
                    </div>
                </div>

                {{-- Analyses Selection --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 p-6 flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Medical Analyses</h2>
                    </div>

                    {{-- Search Bar --}}
                    <div class="p-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="analysisSearch"
                                   @input="searchAnalyses"
                                   @focus="showAnalysisSuggestions = true"
                                   placeholder="Search and add analyses..."
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl 
                                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                   autocomplete="off">

                            {{-- Suggestions --}}
                            <div x-show="showAnalysisSuggestions && analysisSuggestions.length > 0"
                                 x-transition
                                 class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="analysis in analysisSuggestions" :key="analysis.id">
                                    <div @click="addAnalysis(analysis)"
                                         class="p-4 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                        <div class="font-medium text-gray-900" x-text="analysis.name"></div>
                                        <div class="text-sm text-indigo-600 font-semibold mt-1">
                                            <span x-text="analysis.price"></span> DA
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Selected Analyses --}}
                    <div class="p-6">
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Analysis</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price (DA)</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                               <template x-for="(analysis, index) in selectedAnalyses" :key="`analysis_${analysis.analysis_id}_${index}`">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900" x-text="analysis.name"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                            x-text="analysis.code"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <input type="number"
                                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            step="0.01"
                                            x-model.number="analysis.price"
                                            @input="calculateTotals">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button"
                                                @click="removeAnalysis(index)"
                                                class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="xl:col-span-1 space-y-6 xl:sticky xl:top-6 self-start">

                {{-- Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Summary</h3>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="quotation.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="draft">📝 Draft</option>
                            <option value="validated">✅ Validated</option>
                            <option value="converted">🔄 Converted</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm font-medium text-blue-700">Subtotal</span>
                            <span class="font-bold text-blue-900" x-text="total.toFixed(2) + ' DA'"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                            <span class="text-sm font-medium text-indigo-700">Net Total</span>
                            <span class="font-bold text-indigo-900" x-text="netTotal.toFixed(2) + ' DA'"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-red-700">Outstanding</span>
                            <span class="font-bold text-red-900" x-text="outstanding.toFixed(2) + ' DA'"></span>
                        </div>
                    </div>
                </div>

                {{-- Payments --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Payments</h3>
                    </div>

                    {{-- Existing Payments --}}
                    <div class="space-y-3 mb-6">
                        <template x-for="payment in quotation.payments" :key="payment.id">
                            <div class="p-3 rounded-lg bg-green-50 border border-green-200">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-green-700">Received</span>
                                    <span class="font-bold text-green-900"
                                          x-text="(payment.amount_received ?? 0).toFixed(2) + ' DA'"></span>
                                </div>
                                <div class="text-xs text-green-600"
                                     x-text="'Via ' + (payment.method || 'N/A')"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Add Payment --}}
                    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                        <h4 class="font-medium text-emerald-800 mb-3">Add New Payment</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number"
                                       step="0.01"
                                       x-model="newPayment.amount"
                                       @input="calculateTotals"
                                       placeholder="0.00"
                                       class="px-3 py-2 border border-emerald-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <select x-model="newPayment.method"
                                        class="px-3 py-2 border border-emerald-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="cash">💵 Cash</option>
                                    <option value="card">💳 Card</option>
                                    <option value="bank">🏦 Transfer</option>
                                </select>
                            </div>
                            <textarea x-model="newPayment.notes"
                                      placeholder="Payment notes..."
                                      rows="2"
                                      class="w-full px-3 py-2 border border-emerald-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full bg-[#bc1622] text-white font-semibold py-3 px-6 rounded-xl 
                                   hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-200 
                                   transition-all duration-200 shadow-md">
                        💾 Update Quotation
                    </button>
                    <a href="{{ route('quotations.show', $quotation['id']) }}"
                       class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium 
                              hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
function quotationEditForm(quotation, analyses) {
    return {
        // Initialize all required properties
        quotation: quotation || {},
        analyses: analyses || [],
        selectedAnalyses: [],
        analysisSearch: '',
        analysisSuggestions: [],
        showAnalysisSuggestions: false,
        total: 0,
        netTotal: 0,
        outstanding: 0,
        newPayment: { 
            amount: 0, 
            method: 'cash', 
            notes: '' 
        },

        // Add the missing init() method
        init() {
            console.log('Initializing quotation editor', this.quotation);
            
            // Initialize selectedAnalyses with proper unique keys
            this.selectedAnalyses = (this.quotation.analysis_items || []).map((item, index) => ({
                analysis_id: item.analysis?.id || item.id || `temp_${index}`,
                name: item.analysis?.name || item.name || 'Unknown Analysis',
                code: item.analysis?.code || item.code || 'N/A',
                price: parseFloat(item.price || 0)
            }));
            
            // Calculate initial totals
            this.calculateTotals();
        },

        async searchAnalyses() {
            if (this.analysisSearch.length < 2) { 
                this.analysisSuggestions = []; 
                return; 
            }
            
            try {
                const response = await fetch(`/quotations/search-analyses?q=${encodeURIComponent(this.analysisSearch)}`);
                if (response.ok) {
                    this.analysisSuggestions = await response.json();
                } else {
                    console.error('Failed to search analyses');
                    this.analysisSuggestions = [];
                }
            } catch (error) {
                console.error('Error searching analyses:', error);
                this.analysisSuggestions = [];
            }
        },

        addAnalysis(analysis) {
            // Check if analysis already exists using analysis_id
            const exists = this.selectedAnalyses.find(a => a.analysis_id === analysis.id);
            if (!exists) {
                this.selectedAnalyses.push({
                    analysis_id: analysis.id,
                    name: analysis.name,
                    code: analysis.code || 'N/A',
                    price: parseFloat(analysis.price || 0)
                });
                this.calculateTotals();
            }
            this.analysisSearch = '';
            this.showAnalysisSuggestions = false;
        },

        removeAnalysis(index) {
            this.selectedAnalyses.splice(index, 1);
            this.calculateTotals();
        },

        calculateTotals() {
            // Calculate subtotal from selected analyses
            this.total = this.selectedAnalyses.reduce((sum, analysis) => {
                return sum + parseFloat(analysis.price || 0);
            }, 0);
            
            // Calculate net total (same as total for now)
            this.netTotal = this.total;
            
            // Calculate payments received
            const receivedPayments = (this.quotation.payments || []).reduce((sum, payment) => {
                return sum + parseFloat(payment.amount_received || 0);
            }, 0);
            
            // Add new payment amount
            const newPaymentAmount = parseFloat(this.newPayment.amount || 0);
            
            // Calculate outstanding
            this.outstanding = this.total - receivedPayments - newPaymentAmount;
        },

        async submitForm() {
            const payload = {
                patient_id: this.quotation.patient?.id,
                status: this.quotation.status,
                analyses: this.selectedAnalyses.map(analysis => ({ 
                    analysis_id: analysis.analysis_id, 
                    price: parseFloat(analysis.price || 0) 
                })),
                total: this.total,
                new_payment: this.newPayment.amount > 0 ? this.newPayment : null
            };

            try {
                const response = await fetch(`/quotations/${this.quotation.id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    window.location.href = `/quotations/${this.quotation.id}`;
                } else {
                    const data = await response.json();
                    alert(data.message || 'Failed to update quotation.');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('An error occurred while updating the quotation.');
            }
        }
    };
}
</script>
@endsection
