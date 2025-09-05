@extends('layouts.app')

@section('content')
<div x-data="quotationForm()" class="max-w-5xl mx-auto px-6 py-10 space-y-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
            <h1 class="text-2xl font-bold text-gray-800">New Quotation</h1>
            <p class="text-sm text-gray-600 mt-1">Fill in the details below to create a new laboratory quotation.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-6 rounded-md">
                <h3 class="text-sm font-semibold text-red-800">
                    {{ $errors->count() }} error(s) with your submission
                </h3>
                <ul class="mt-2 text-sm text-red-700 list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quotations.store') }}" method="POST" id="quotation-form" class="divide-y divide-gray-200">
            @csrf

            <!-- Patient Section -->
            <section class="p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    👤 Patient Information
                </h2>
                <p class="text-sm text-gray-500">Select an existing patient or create a new one.</p>

                <select name="patient_id" id="patient_id"
                        class="w-full rounded-lg border-gray-300 py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Select Existing Patient --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient['id'] }}">{{ $patient['first_name'] }} {{ $patient['last_name'] }}</option>
                    @endforeach
                </select>

                <button type="button"
                    class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                    @click="showNewPatientForm = !showNewPatientForm">
                    + Add New Patient
                </button>

                <!-- Inline new patient form -->
                <div x-show="showNewPatientForm" x-transition class="mt-4 space-y-4 bg-gray-50 p-4 rounded-md border">
                    <h3 class="text-sm font-semibold text-gray-800">New Patient Details</h3>
                    <div class="grid grid-cols-6 gap-4">
                        <input type="text" name="new_patient[first_name]" placeholder="First Name"
                               class="col-span-3 rounded-md border-gray-300">
                        <input type="text" name="new_patient[last_name]" placeholder="Last Name"
                               class="col-span-3 rounded-md border-gray-300">
                        <input type="date" name="new_patient[dob]" class="col-span-3 rounded-md border-gray-300">
                        <select name="new_patient[blood_type]" class="col-span-3 rounded-md border-gray-300">
                            <option value="">Blood Type</option>
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 bg-gray-100 rounded-md" @click="showNewPatientForm=false">Cancel</button>
                        <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md">Save Patient</button>
                    </div>
                </div>
            </section>

            <!-- Analyses Section -->
            <section class="p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">Analyses</h2>
                <p class="text-sm text-gray-500">Add laboratory analyses to this quotation.</p>
                <div id="analysis-rows" class="space-y-3"></div>
                <button type="button" id="add-analysis"
                    class="mt-2 inline-flex items-center px-4 py-2 rounded-md border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50">
                    + Add Analysis
                </button>
            </section>

            <!-- Agreement Section -->
            <section class="p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">Agreement</h2>
                <p class="text-sm text-gray-500">Apply a discount agreement if applicable.</p>
                <select name="agreement_id" id="agreement_id"
                        class="w-full rounded-lg border-gray-300 py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- No Agreement --</option>
                    @foreach($agreements as $agreement)
                        <option value="{{ $agreement['id'] }}">
                            {{ ucfirst($agreement['discount_type']) }} - {{ $agreement['discount_value'] }}{{ $agreement['discount_type'] === 'percentage' ? '%' : ' DZD' }}
                        </option>
                    @endforeach
                </select>
            </section>

            <section class="p-6 space-y-4" id="payment-section">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium">Payment Method</label>
                    <select id="payment-method" name="payment[method]" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">-- Select Method --</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="transfer">Bank Transfer</option>
                    </select>
                </div>

                <!-- Amount Received -->
                <div>
                    <label class="block text-sm font-medium">Amount Reçu</label>
                    <input type="number" step="0.01" name="payment[amount_received]" id="payment-amount"
                        placeholder="0.00" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gray-50 p-4 rounded-md border mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Sous-total:</span>
                    <span id="subtotal-display">DZD 0.00</span>
                </div>

                <div id="discount-row" class="flex justify-between text-sm hidden">
                    <span>Remise:</span>
                    <span id="discount-display">-DZD 0.00</span>
                </div>

                <div class="flex justify-between text-base font-semibold border-t pt-2 mt-2">
                    <span>Total à payer:</span>
                    <span id="total-display">DZD 0.00</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span>Reçu:</span>
                    <span id="received-display" class="text-green-600 font-medium">DZD 0.00</span>
                </div>

                <div class="flex justify-between text-sm" id="change-row" style="display:none;">
                    <span>Monnaie:</span>
                    <span id="change-display" class="text-gray-900 font-medium">DZD 0.00</span>
                </div>
                <div class="flex justify-between text-base font-semibold border-t pt-2 mt-2"> 
                    <span>Restant:</span> 
                    <span id="outstanding-display" class="text-red-600">DZD 0.00</span> 
                </div>
            </div>

            <!-- Hidden inputs for backend -->
            <input type="hidden" name="payment[amount]" id="payment-amount-hidden">
            <input type="hidden" name="payment[change_given]" id="payment-change-hidden">
            <input type="hidden" name="payment[notes]" value="Created at quotation"> <!-- Optional default -->
        </section>

            <!-- Submit -->
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button type="submit"
                        class="inline-flex items-center gap-2 py-3 px-6 rounded-lg bg-green-600 text-white text-lg font-medium hover:bg-green-700 focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow">
                    ✅ Create Quotation
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

    const paymentMethodEl = document.getElementById('payment-method');
    const paymentAmountEl = document.getElementById('payment-amount');

    const subtotalDisplay = document.getElementById('subtotal-display');
    const discountRow = document.getElementById('discount-row');
    const discountDisplay = document.getElementById('discount-display');
    const totalDisplay = document.getElementById('total-display');
    const receivedDisplay = document.getElementById('received-display');
    const changeRow = document.getElementById('change-row');
    const changeDisplay = document.getElementById('change-display');
    const outstandingDisplay = document.getElementById('outstanding-display');
    const agreementSelect = document.querySelector('select[name="agreement_id"]');

    let subtotal = 0;
    let discount = 0;
    let total = 0;

    function formatCurrency(val) {
        return parseFloat(val || 0).toFixed(2);
    }

    // Calculate subtotal, discount, and total
    function calculateTotal() {
        subtotal = 0;

        document.querySelectorAll('.analysis-price').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });

        // Calculate discount
        const agreementId = agreementSelect.value;
        discount = 0;

        if (agreementId) {
            const agreement = agreements.find(a => a.id == agreementId);
            if (agreement) {
                if (agreement.discount_type === 'percentage') {
                    discount = subtotal * (agreement.discount_value / 100);
                } else if (agreement.discount_type === 'fixed') {
                    discount = agreement.discount_value;
                }
                discountRow.style.display = 'flex';
                discountDisplay.textContent = `-DZD ${formatCurrency(discount)}`;
            } else {
                discountRow.style.display = 'none';
            }
        } else {
            discountRow.style.display = 'none';
        }

        total = Math.max(subtotal - discount, 0);

        subtotalDisplay.textContent = `DZD ${formatCurrency(subtotal)}`;
        totalDisplay.textContent = `DZD ${formatCurrency(total)}`;

        updatePaymentSummary();
    }

    // Update payment summary dynamically
   function updatePaymentSummary() {
        const payment = parseFloat(paymentAmountEl.value) || 0;
        const method = paymentMethodEl.value;

        // amount_received = user entry
        document.getElementById('payment-amount').value = payment;
        document.getElementById('payment-amount-hidden').value = total;

        receivedDisplay.textContent = `DZD ${formatCurrency(payment)}`;

        let change = 0;
        if (method === 'cash') {
            change = Math.max(payment - total, 0);
            changeRow.style.display = 'flex';
            changeDisplay.textContent = `DZD ${formatCurrency(change)}`;
        } else {
            changeRow.style.display = 'none';
        }

        // Hidden field for backend
        document.getElementById('payment-change-hidden').value = change;
    }

    // Event listeners for payment inputs
    paymentAmountEl.addEventListener('input', updatePaymentSummary);
    paymentMethodEl.addEventListener('change', updatePaymentSummary);
    agreementSelect.addEventListener('change', calculateTotal);

    // Analysis row management
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
                <select name="items[${rowCount}][analysis_id]" class="block w-full rounded-md border-gray-300 py-2 px-3 analysis-select" required>
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
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-3 py-2 bg-red-600 text-sm font-medium text-white hover:bg-red-700 remove-row">
                    &times;
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
            const price = selected ? selected.getAttribute('data-price') : 0;
            priceInput.value = formatCurrency(price);
            calculateTotal();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('.analysis-row');
            row.remove();
            calculateTotal();
        }
    });

    document.getElementById('add-analysis').addEventListener('click', () => {
        addAnalysisRow();
    });

    // Initialize with one row
    addAnalysisRow();
});
</script>
@endpush