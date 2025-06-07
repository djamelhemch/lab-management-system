@extends('layouts.app')

@section('content')
<div x-data="quotationForm()" class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">New Quotation</h1>
            <p class="text-sm text-gray-600 mt-1">Create a new laboratory quotation</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mx-6 mt-6 rounded">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('quotations.store') }}" method="POST" id="quotation-form" class="divide-y divide-gray-200">
            @csrf

            <!-- Patient Section -->
            <div class="p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Patient Information</h2>
                    <p class="mt-1 text-sm text-gray-500">Search for an existing patient or add a new one</p>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Patient Search -->
                    <div class="sm:col-span-4">
                        <label for="patient-search" class="block text-sm font-medium text-gray-700">Search Patient</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input 
                                type="text"
                                id="patient-search"
                                x-model="searchQuery"
                                @input.debounce.300ms="searchPatients"
                                placeholder="Type patient name or file number..."
                                class="block w-full rounded-md border-gray-300 pl-4 pr-10 py-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Select Dropdown -->
                    <div class="sm:col-span-4">
                        <label for="patient_id" class="block text-sm font-medium text-gray-700">Select Patient</label>
                        <select 
                            name="patient_id" 
                            id="patient_id" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                            <option value="" disabled selected>-- Select a patient --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient['id'] }}">
                                    {{ $patient['first_name'] }} {{ $patient['last_name'] }} ({{ $patient['file_number'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Add New Patient Button -->
                    <div class="sm:col-span-2 flex items-end">
                        <button 
                            type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            @click="showNewPatientForm = !showNewPatientForm"
                        >
                            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Patient
                        </button>
                    </div>

                    <!-- New Patient Form (Hidden by default) -->
                    <div x-show="showNewPatientForm" x-transition class="sm:col-span-6 bg-gray-50 p-4 rounded-md border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-800 mb-3">New Patient Details</h3>
                        <div class="grid grid-cols-1 gap-y-4 gap-x-6 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="new_patient[first_name]" id="first_name" placeholder="First Name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" :required="showNewPatientForm">
                            </div>

                            <div class="sm:col-span-3">
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="new_patient[last_name]" id="last_name" placeholder="Last Name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" :required="showNewPatientForm">
                            </div>

                            <div class="sm:col-span-3">
                                <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="new_patient[dob]" id="dob" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3" :required="showNewPatientForm">
                            </div>

                            <div class="sm:col-span-3">
                                <label for="blood_type" class="block text-sm font-medium text-gray-700">Blood Type</label>
                                <select id="blood_type" name="new_patient[blood_type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3">
                                    <option value="">Select blood type</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" @click="showNewPatientForm = false">
                                Cancel
                            </button>
                            <button type="button" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Patient
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analyses Section -->
            <div class="p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Analyses</h2>
                    <p class="mt-1 text-sm text-gray-500">Add laboratory analyses to this quotation</p>
                </div>

                <div id="analysis-rows" class="space-y-4">
                    <!-- Analysis rows will be added here dynamically -->
                </div>

                <div>
                    <button 
                        type="button" 
                        id="add-analysis" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Analysis
                    </button>
                </div>
            </div>

            <!-- Agreement Section -->
            <div class="p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Agreement</h2>
                    <p class="mt-1 text-sm text-gray-500">Apply a discount agreement if applicable</p>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="agreement_id" class="block text-sm font-medium text-gray-700">Select Agreement</label>
                        <select name="agreement_id" id="agreement_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- No Agreement --</option>
                            @foreach($agreements as $agreement)
                                <option value="{{ $agreement['id'] }}">
                                    {{ ucfirst($agreement['discount_type']) }} - 
                                    {{ $agreement['discount_value'] }}{{ $agreement['discount_type'] === 'percentage' ? '%' : '€' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="p-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Summary</h2>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="bg-white p-4 rounded-md shadow-sm border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-3">Amount Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span class="text-sm font-medium text-gray-900" id="subtotal-display">DZD 0.00</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-2" id="discount-row" style="display: none;">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span class="text-sm font-medium text-red-600" id="discount-display">-DZD 0.00</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-2">
                                <span class="text-base font-medium text-gray-900">Total:</span>
                                <span class="text-base font-semibold text-gray-900" id="total-display">DZD 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button 
                    type="submit" 
                    class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-lg font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Create Quotation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quotationForm() {
    return {
        showNewPatientForm: false,
        showPatientModal: false,
        searchQuery: '',

        searchPatients() {
            // Optional: implement AJAX search logic
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const availableAnalyses = @json($analyses);
    const agreements = @json($agreements);
    let rowCount = 0;

    const subtotalDisplay = document.querySelector('#subtotal-display');
    const discountRow = document.querySelector('#discount-row');
    const discountDisplay = document.querySelector('#discount-display');
    const totalDisplay = document.querySelector('#total-display');
    const agreementSelect = document.querySelector('select[name="agreement_id"]');

    function formatCurrency(val) {
        return parseFloat(val || 0).toFixed(2);
    }

    function calculateTotal() {
        let subtotal = 0;
        document.querySelectorAll('.analysis-price').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });

        const agreementId = agreementSelect.value;
        let discount = 0;
        let total = subtotal;

        if (agreementId) {
            const agreement = agreements.find(a => a.id == agreementId);
            if (agreement) {
                if (agreement.discount_type === 'percentage') {
                    discount = subtotal * (agreement.discount_value / 100);
                } else if (agreement.discount_type === 'fixed') {
                    discount = agreement.discount_value;
                }
                total = subtotal - discount;
                
                // Show discount row
                discountRow.style.display = 'flex';
                discountDisplay.textContent = `-DZD ${formatCurrency(discount)}`;
            } else {
                discountRow.style.display = 'none';
            }
        } else {
            discountRow.style.display = 'none';
        }

        // Update displays
        subtotalDisplay.textContent = `DZD ${formatCurrency(subtotal)}`;
        totalDisplay.textContent = `DZD ${formatCurrency(total)}`;
    }

    function addAnalysisRow(selectedId = '', price = '') {
        rowCount++;
        const row = document.createElement('div');
        row.classList.add('grid', 'grid-cols-1', 'sm:grid-cols-12', 'gap-4', 'items-center', 'analysis-row');
        row.setAttribute('data-row', rowCount);

        const options = availableAnalyses.map(analysis => {
            return `<option value="${analysis.id}" data-price="${analysis.price}" ${analysis.id == selectedId ? 'selected' : ''}>
                        ${analysis.name} (DZD ${analysis.price.toFixed(2)})
                    </option>`;
        }).join('');

        row.innerHTML = `
            <div class="sm:col-span-8">
                <select name="items[${rowCount}][analysis_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 analysis-select" required>
                    <option value="">-- Select Analysis --</option>
                    ${options}
                </select>
            </div>
            <div class="sm:col-span-3">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">DZD</span>
                    </div>
                    <input type="text" name="items[${rowCount}][price]" class="block w-full rounded-md border-gray-300 py-2 pl-12 pr-3 text-right sm:text-sm analysis-price" value="${formatCurrency(price)}" readonly required>
                </div>
            </div>
            <div class="sm:col-span-1">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-3 py-2 bg-red-600 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 remove-row">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;

        document.getElementById('analysis-rows').appendChild(row);
        calculateTotal();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('analysis-select')) {
            const priceInput = e.target.closest('.analysis-row').querySelector('.analysis-price');
            const selected = e.target.selectedOptions[0];
            const price = selected.getAttribute('data-price') || 0;
            priceInput.value = formatCurrency(price);
            calculateTotal();
        }

        if (e.target.name === 'agreement_id') {
            calculateTotal();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const rowToRemove = e.target.closest('.remove-row') ? e.target.closest('.analysis-row') : e.target.closest('.analysis-row');
            rowToRemove.remove();
            calculateTotal();
        }
    });

    document.getElementById('add-analysis').addEventListener('click', () => {
        addAnalysisRow();
    });

    // Initialize with one empty row
    addAnalysisRow();
});
</script>
@endpush