{{-- resources/views/quotations/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Quotation #{{ $quotation['id'] }}</h1>
        <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back</a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6" x-data="quotationFormEdit({{ json_encode($quotation) }})">
        <form @submit.prevent="submitForm">
            <!-- Patient Selection (readonly for edit, or you can allow change) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                <input type="text" x-model="patientName" class="w-full p-3 border border-gray-300 rounded" readonly>
            </div>

            <!-- Analysis Selection (same as create) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Analysis</label>
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="analysisSearch"
                        @input="searchAnalyses"
                        @focus="showAnalysisSuggestions = true"
                        placeholder="Search and select analyses..."
                        class="w-full p-3 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                        autocomplete="off"
                    >
                    <div x-show="showAnalysisSuggestions && analysisSuggestions.length > 0" 
                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-b-lg shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="analysis in analysisSuggestions" :key="analysis.id">
                            <div @click="addAnalysis(analysis)" 
                                 class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                                <div class="font-medium" x-text="analysis.name"></div>
                                <div class="text-sm text-gray-600">Price: $<span x-text="analysis.price"></span></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Selected Analyses -->
            <div class="mb-6" x-show="selectedAnalyses.length > 0">
                <h3 class="text-lg font-medium mb-3">Selected Analyses</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <template x-for="(analysis, index) in selectedAnalyses" :key="analysis.id">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                            <div>
                                <div class="font-medium" x-text="analysis.name"></div>
                                <div class="text-sm text-gray-600" x-text="analysis.description"></div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input 
                                    type="number" 
                                    x-model="analysis.price" 
                                    @input="calculateTotal"
                                    step="0.01" 
                                    min="0"
                                    class="w-24 p-1 border border-gray-300 rounded text-right"
                                >
                                <button type="button" @click="removeAnalysis(index)" 
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="status" class="w-full p-3 border border-gray-300 rounded">
                    <option value="draft">Draft</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="converted">Converted</option>
                </select>
            </div>

            <!-- Total -->
            <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-medium">Total Amount:</span>
                    <span class="text-2xl font-bold text-blue-600">$<span x-text="total.toFixed(2)"></span></span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
                    Update Quotation
                </button>
                <a href="{{ route('quotations.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function quotationFormEdit(quotation) {
    return {
        patientName: quotation.patient.full_name,
        selectedAnalyses: quotation.quotation_items.map(item => ({
            id: item.analysis.id,
            name: item.analysis.name,
            description: item.analysis.description,
            price: item.price
        })),
        analysisSearch: '',
        analysisSuggestions: [],
        showAnalysisSuggestions: false,
        status: quotation.status,
        total: quotation.total,

        async searchAnalyses() {
            if (this.analysisSearch.length < 2) {
                this.analysisSuggestions = [];
                return;
            }
            try {
                const response = await fetch(`/quotations/search-analyses?q=${encodeURIComponent(this.analysisSearch)}`);
                this.analysisSuggestions = await response.json();
            } catch (error) {
                console.error('Error searching analyses:', error);
            }
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
            this.total = this.selectedAnalyses.reduce((sum, analysis) => {
                return sum + parseFloat(analysis.price || 0);
            }, 0);
        },

        async submitForm() {
            if (this.selectedAnalyses.length === 0) {
                alert('Please select at least one analysis.');
                return;
            }

            const formData = new FormData();
            formData.append('patient_id', quotation.patient.id);
            formData.append('status', this.status);

            this.selectedAnalyses.forEach((analysis, index) => {
                formData.append(`analyses[${index}][analysis_id]`, analysis.id);
                formData.append(`analyses[${index}][price]`, analysis.price);
            });

            formData.append('total', this.total);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            try {
                const response = await fetch('{{ route("quotations.update", $quotation["id"]) }}', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.href = '{{ route("quotations.index") }}';
                } else {
                    alert('Error updating quotation. Please try again.');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Error updating quotation. Please try again.');
            }
        }
    }
}
</script>
@endsection