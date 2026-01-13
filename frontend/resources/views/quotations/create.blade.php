@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div x-data="quotationForm()" class="max-w-6xl mx-auto px-4">

        {{-- Progress Header --}}
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Create New Quotation</h1>
                            <p class="text-sm text-gray-500">Fill in the details to generate a laboratory quotation</p>
                        </div>

                        {{-- Step Progress Indicator --}}
                        <div class="hidden md:flex items-center gap-3 text-sm text-gray-600">
                            <span class="font-medium">Patient</span>
                            <div class="w-6 h-px bg-gray-300"></div>
                            <span>Analyses</span>
                            <div class="w-6 h-px bg-gray-300"></div>
                            <span>Payment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Display --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <h3 class="text-sm font-semibold text-red-800 mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
                
        {{-- Today's Latest Visits - NEW SECTION --}}
        @if($todayVisits->isNotEmpty())
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

                    {{-- Header --}}
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Today's Visits ({{ $todayVisits->count() }})
                            </h3>
                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            Latest quotations today – Click to quick-select patient
                        </p>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-20">
                                        Time
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-12">
                                        Date
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                        Patient
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-32">
                                        File #
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @foreach($todayVisits->take(15) as $visit)
                                    <tr
                                        class="hover:bg-blue-50 cursor-pointer transition-all duration-200 group
                                            border-l-4 border-l-transparent hover:border-l-blue-400 hover:shadow-sm"
                                        onclick="quickSelectPatient({{ $visit['patient_id'] ?? 'null' }})"
                                    >
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($visit['visit_time'])->format('H:i') }}
                                        </td>

                                        <td class="px-4 py-3 text-xs font-mono text-gray-600">
                                            {{ \Carbon\Carbon::parse($visit['visit_date'])->format('d/m') }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="truncate group-hover:underline max-w-[200px]">
                                                {{ trim($visit['patient_first_name'].' '.$visit['patient_last_name']) ?: 'N/A' }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-xs font-mono bg-gray-50 text-gray-800 rounded truncate">
                                            {{ $visit['file_number'] ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer --}}
                    @if($todayVisits->count() > 15)
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-xs text-center text-gray-500 font-medium">
                            +{{ $todayVisits->count() - 15 }} more today
                        </div>
                    @endif

                </div>
            </div>
        @else
            {{-- Empty state --}}
            <div class="mb-6">
                <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        Aucun devis aujourd’hui
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Aucune visite n’a encore été créé aujourd’hui.<br>
                        Les nouveaux devis apparaîtront ici automatiquement.
                    </p>
                </div>
            </div>
        @endif

        {{-- Quotation Form --}}
        <form action="{{ route('quotations.store') }}" method="POST" id="quotation-form" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Main Form Content --}}
                <div class="xl:col-span-2 space-y-6">

                    {{-- Patient Section --}}
                <div class="bg-white rounded-xl shadow-md border border-gray-200" x-data="patientForm()">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Patient Information</h2>
                    <p class="text-sm text-gray-500">Select existing patient or create new</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Patient Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Patient *</label>
                        <select name="patient_id" id="patient_id"
                            class="w-full px-3 py-2 border rounded-lg focus:border-indigo-500 focus:ring-0">
                            <option value="">-- Sélectionnez un patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient['id'] }}">{{ $patient['first_name'] }} {{ $patient['last_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Add New Patient Toggle --}}
                    <div class="flex items-center justify-center mt-2">
                        <button type="button"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                            @click="showNewPatient = !showNewPatient">
                            + Ajouter un nouveau patient
                        </button>
                    </div>
                        <!-- keep the inner form for @submit.prevent but note required bindings are conditional -->
                        <div 
                            x-show="showNewPatient" 
                            x-cloak 
                            class="bg-gray-50 rounded-lg p-6 border border-gray-200 space-y-4"
                        >
                            <h3 class="text-sm font-semibold text-gray-700">Nouveau Patient</h3>

                            <div x-show="showNewPatient" x-cloak>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Prénom -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Prénom *</label>
                                        <input type="text" x-model="form.first_name" :required="showNewPatient"
                                            class="p-3 border border-gray-300 rounded-lg w-full"/>
                                    </div>

                                    <!-- Nom -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Nom *</label>
                                        <input type="text" x-model="form.last_name" :required="showNewPatient"
                                            class="p-3 border border-gray-300 rounded-lg w-full"/>
                                    </div>

                                    <!-- Date de naissance -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Date de naissance *</label>
                                        <input type="date" x-model="form.dob" :required="showNewPatient"
                                            class="p-3 border border-gray-300 rounded-lg w-full"/>
                                    </div>

                                    <!-- Sexe -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Sexe *</label>
                                        <select x-model="form.gender" :required="showNewPatient"
                                                class="p-3 border border-gray-300 rounded-lg w-full">
                                            <option value="">Sélectionnez le sexe</option>
                                            <option value="H">Homme</option>
                                            <option value="F">Femme</option>
                                        </select>
                                    </div>

                                    <!-- Téléphone -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Téléphone</label>
                                        <input type="text" x-model="form.phone" id="phoneField"
                                            class="p-3 border border-gray-300 rounded-lg w-full"/>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Email</label>
                                        <input type="email" x-model="form.email"
                                            class="p-3 border border-gray-300 rounded-lg w-full"/>
                                    </div>

                                    <!-- Adresse -->
                                    <div class="md:col-span-2">
                                        <label class="block mb-1 font-semibold text-gray-700">Adresse</label>
                                        <textarea x-model="form.address"
                                            class="p-3 border border-gray-300 rounded-lg w-full"></textarea>
                                    </div>

                                    <!-- Groupe sanguin -->
                                    <div>
                                        <label for="blood_type" class="block mb-1 font-semibold text-gray-700">
                                            Groupe sanguin <span class="text-red-500">*</span>
                                        </label>
                                        <select id="blood_type" x-model="form.blood_type" :required="showNewPatient"
                                            class="p-3 border border-gray-300 rounded-lg w-full">
                                            <option value="">-- Sélectionnez un groupe sanguin --</option>
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

                                    <!-- Poids -->
                                    <div>
                                        <label class="block mb-1 font-semibold text-gray-700">Poids (kg)</label>
                                        <input type="number" step="0.1" x-model="form.weight" :required="showNewPatient"
                                            class="p-3 border border-gray-300 rounded-lg w-full"/>
                                    </div>

                                    <!-- Allergies -->
                                    <div class="md:col-span-2">
                                        <label class="block mb-1 font-semibold text-gray-700">Allergies</label>
                                        <textarea x-model="form.allergies"
                                            class="p-3 border border-gray-300 rounded-lg w-full"></textarea>
                                    </div>

                                    <!-- Antécédents médicaux -->
                                    <div class="md:col-span-2">
                                        <label class="block mb-1 font-semibold text-gray-700">Antécédents médicaux</label>
                                        <textarea x-model="form.medical_history"
                                            class="p-3 border border-gray-300 rounded-lg w-full"></textarea>
                                    </div>

                                    <!-- Maladies chroniques -->
                                    <div class="md:col-span-2">
                                        <label class="block mb-1 font-semibold text-gray-700">Maladies chroniques</label>
                                        <textarea x-model="form.chronic_conditions"
                                            class="p-3 border border-gray-300 rounded-lg w-full"></textarea>
                                    </div>

                                    
                                    {{-- Doctor --}}
                                    <div class="md:col-span-2">
                                        <label for="doctor_id" class="block mb-1 font-semibold text-gray-700">Médecin Traitant</label>

                                        <div class="flex gap-4 items-start">

                                            {{-- Select --}}
                                            <div class="flex-1">
                                                <select id="doctor_id" name="doctor_id"
                                                    class="searchable-select p-3 border border-gray-300 rounded-xl w-full 
                                                        focus:ring-2 focus:ring-red-300 focus:border-red-500 transition-all 
                                                        bg-white shadow-sm hover:border-red-400">
                                                    <option value="" disabled selected>Choisissez un médecin</option>
                                                    @foreach($doctors as $doctor)
                                                        <option value="{{ $doctor['id'] }}">{{ $doctor['full_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Create Doctor Button --}}
                                            <button type="button" 
                                                id="openDoctorModal"
                                                class="px-4 py-2 flex items-center gap-2 rounded-xl border border-red-300 
                                                    text-red-600 font-medium bg-red-50 hover:bg-red-100 hover:border-red-400 
                                                    transition-all shadow-sm">
                                                <span class="text-lg">➕</span>
                                                <span class="text-sm">Nouveau</span>
                                            </button>

                                        </div>
                            
                                
                                        @error('doctor_id') 
                                            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> 
                                        @enderror
                                    </div>

                                <div class="flex justify-between mt-4">
                                    <button type="button" @click="resetForm"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                        Annuler
                                    </button>

                                    <button type="button" @click="validateBeforeSubmit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                        x-text="loading ? 'Saving...' : 'Save Patient'"
                                        :disabled="loading">
                                    </button>
                                </div>
                           </div>
                        </div>
                    </div>
            </div>
    </div>
                <!-- Popup Modal -->
                <div id="phonePopup" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-xl shadow-lg max-w-sm text-center">
                        <h3 class="text-lg font-bold mb-4 text-gray-800">⚠️ Pas de numéro de téléphone !</h3>
                        <p class="text-gray-600 mb-6">Vous pouvez l'ajouter si nécessaire.</p>
                        <div class="flex justify-center gap-4">
                            <button id="ignoreBtn" type="button"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                Ignorer et continuer
                            </button>
                            <button id="addBtn" type="button"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
                                Ajouter numéro
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
     
        Queue Settings
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Priority Selection --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-exclamation-circle text-indigo-500 mr-1"></i> 
                Queue Priority
            </label>
            <select name="priority" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="0" selected>
                    Normal - Standard processing
                </option>
                <option value="1">
                    Urgent - Priority processing
                </option>
                <option value="2">
                    Emergency - Immediate attention
                </option>
            </select>
        </div>

        {{-- Info Box --}}
        <div class="flex items-center bg-blue-50 rounded-xl p-4">
            <i class="fas fa-info-circle text-blue-500 text-2xl mr-3"></i>
            <div>
                <h4 class="font-semibold text-blue-900 text-sm">Automatic Queue Add</h4>
                <p class="text-xs text-blue-800">
                    Patient will be added to reception queue after quotation creation
                </p>
            </div>
        </div>
    </div>
</div>

                    {{-- Analyses Section --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Medical Analyses</h2>
                            <p class="text-sm text-gray-500">Add laboratory tests and procedures</p>
                        </div>
                        <div class="p-6">
                            <div id="analysis-rows" class="space-y-4"></div>
                            <div class="mt-4">
                                <button type="button" id="add-analysis"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                    + Add Analysis
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Agreement Section --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Discount Agreement</h2>
                            <p class="text-sm text-gray-500">Apply discounts if applicable</p>
                        </div>
                        <div class="p-6">
                            <select name="agreement_id" id="agreement_id"
                                    class="w-full px-3 py-2 border rounded-lg focus:border-indigo-500 focus:ring-0">
                                <option value="">-- No Discount Applied --</option>
                                @foreach($agreements as $agreement)
                                    <option value="{{ $agreement['id'] }}">
                                        {{ ucfirst($agreement['discount_type']) }} -
                                        {{ $agreement['discount_value'] }}{{ $agreement['discount_type'] === 'percentage' ? '%' : ' DZD' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Sidebar - Payment & Summary --}}
                <div class="xl:col-span-1 space-y-6 xl:sticky xl:top-6 self-start">
                    {{-- Financial Summary --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-3">
                        <h3 class="text-base font-semibold text-gray-800 mb-4">Financial Summary</h3>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span id="subtotal-display" class="font-medium">DZD 0.00</span>
                        </div>
                        <div id="discount-row" class="flex justify-between text-sm hidden">
                            <span class="text-gray-600">Discount</span>
                            <span id="discount-display" class="font-medium text-red-600">-DZD 0.00</span>
                        </div>
                        <div class="flex justify-between text-sm font-semibold border-t pt-2">
                            <span>Total to Pay</span>
                            <span id="total-display">DZD 0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Amount Received</span>
                            <span id="received-display" class="font-medium">DZD 0.00</span>
                        </div>
                        <div id="change-row" class="flex justify-between text-sm hidden">
                            <span class="text-gray-600">Change Due</span>
                            <span id="change-display">DZD 0.00</span>
                        </div>
                        <div class="flex justify-between text-sm font-semibold text-red-600">
                            <span>Outstanding</span>
                            <span id="outstanding-display">DZD 0.00</span>
                        </div>
                    </div>

                    {{-- Payment Section --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-800">Payment Details</h3>
                        <select id="payment-method" name="payment[method]"
                                class="w-full px-3 py-2 border rounded-lg focus:border-indigo-500 focus:ring-0">
                            <option value="">-- Select Method --</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                        </select>
                        <input type="number" step="0.01" name="payment[amount_received]" id="payment-amount"
                               placeholder="0.00"
                               class="w-full px-3 py-2 border rounded-lg focus:border-indigo-500 focus:ring-0 text-right">

                        {{-- Hidden Fields --}}
                        <input type="hidden" name="payment[amount]" id="payment-amount-hidden">
                        <input type="hidden" name="payment[change_given]" id="payment-change-hidden">
                        <input type="hidden" name="payment[notes]" value="Created with quotation">
                    </div>

                    {{-- Submit Button --}}
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-indigo-700">
                            Create Quotation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    
    {{-- Doctor Modal --}}
    <div id="doctorModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="text-xl font-bold text-gray-900">Nouveau Médecin</h3>
                <button type="button" id="closeDoctorModal" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                {{-- Error Message --}}
                <div id="doctorError" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <!-- Error will be inserted here -->
                </div>

                <form id="doctorForm" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Prénom --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"
                                placeholder="Jean">
                        </div>

                        {{-- Nom --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"
                                placeholder="Dupont">
                        </div>

                        {{-- Specialty --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Spécialité
                            </label>
                            <input type="text" name="specialty"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"
                                placeholder="Cardiologue, Généraliste, etc.">
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Téléphone
                            </label>
                            <input type="text" name="phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"
                                placeholder="+213 555 123 456">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Email
                            </label>
                            <input type="email" name="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"
                                placeholder="doctor@example.com">
                        </div>

                        {{-- Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Adresse
                            </label>
                            <textarea name="address" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"
                                placeholder="123 Rue de la Santé, Alger"></textarea>
                        </div>

                        {{-- Is Prescriber --}}
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_prescriber" value="1"
                                    class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-2 focus:ring-red-300">
                                <span class="text-sm font-semibold text-gray-700">
                                    Ce médecin peut prescrire des ordonnances
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-8">
                                Cochez cette case si le médecin est autorisé à rédiger des prescriptions médicales
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" id="closeDoctorModalBtn"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Créer le Médecin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- End Doctor Modal -->
{{-- Toast Notification Container --}}
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3"></div>

{{-- Spinner Overlay (hidden by default) --}}
<div id="spinnerOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-4">
        <svg class="animate-spin h-12 w-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-700 font-medium">Traitement en cours...</p>
    </div>
</div>
@endsection


@push('scripts')

<script>
function patientForm() {
    return {
        showNewPatient: false,
        loading: false,
        form: {
            first_name: '',
            last_name: '',
            dob: '',
            gender: '',
            phone: '',
            email: '',
            address: '',
            blood_type: '',
            weight: '',
            allergies: '',
            medical_history: '',
            chronic_conditions: '',
            doctor_id: ''
        },
        resetForm() {
            this.form = {
                first_name: '',
                last_name: '',
                dob: '',
                gender: '',
                phone: '',
                email: '',
                address: '',
                blood_type: '',
                weight: '',
                allergies: '',
                medical_history: '',
                chronic_conditions: '',
                doctor_id: ''
            };
            this.showNewPatient = false;
        },
        validateBeforeSubmit() {
            // Manual validation
            if (!this.form.first_name || !this.form.last_name || !this.form.dob || !this.form.gender || !this.form.blood_type) {
                alert("⚠️ Merci de remplir tous les champs obligatoires.");
                return;
            }

            if (!this.form.phone) {
                document.getElementById('phonePopup').classList.remove('hidden');

                document.getElementById('ignoreBtn').onclick = () => {
                    document.getElementById('phonePopup').classList.add('hidden');
                    this.submit(); // continue without phone
                };
                document.getElementById('addBtn').onclick = () => {
                    document.getElementById('phonePopup').classList.add('hidden');
                    document.getElementById('phoneField').focus();
                };
                return;
            }

            this.submit();
        },
        async submit() {
            this.loading = true;
            try {
                let response = await fetch("{{ route('patients.ajaxStore') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(this.form)
                });

                let data = await response.json();

                if (!response.ok) {
                    alert("❌ Error: " + JSON.stringify(data.errors || data.error));
                    return;
                }

                // ✅ Add to patient dropdown
                let patientSelect = document.getElementById('patient_id');
                let option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.first_name + " " + data.last_name;
                option.selected = true;
                patientSelect.appendChild(option);

                this.resetForm();
                alert("✅ Patient created and selected!");
            } catch (err) {
                console.error(err);
                alert("Unexpected error creating patient");
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

{{-- DOCTOR MODAL SUBMIT SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========== TOAST NOTIFICATION SYSTEM ==========
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        
        toast.className = `${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0 min-w-[320px]`;
        toast.innerHTML = `
            <div class="flex-shrink-0">${icon}</div>
            <p class="flex-1 font-medium">${message}</p>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => toast.classList.remove('translate-x-full', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ========== SPINNER CONTROLS ==========
    const spinnerOverlay = document.getElementById('spinnerOverlay');
    
    function showSpinner() {
        if (spinnerOverlay) {
            spinnerOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function hideSpinner() {
        if (spinnerOverlay) {
            spinnerOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // ========== MODAL & FORM LOGIC ==========
    const modal = document.getElementById('doctorModal');
    const openModalBtn = document.getElementById('openDoctorModal');
    const closeModalBtn = document.getElementById('closeDoctorModal');
    const closeModalBtn2 = document.getElementById('closeDoctorModalBtn');
    const doctorForm = document.getElementById('doctorForm');
    const doctorSelect = document.getElementById('doctor_id');
    
    if (!modal || !doctorForm || !doctorSelect) {
        console.error('Required elements not found');
        return;
    }
    
    const submitBtn = doctorForm.querySelector('button[type="submit"]');
    let doctorChoices;

    function refreshDoctorChoices() {
        if (doctorChoices) doctorChoices.destroy();
        doctorChoices = new Choices(doctorSelect, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false
        });
    }

    refreshDoctorChoices();

    // ✅ Single close function
    function closeModal() {
        modal.classList.add('hidden');
        doctorForm.reset();
        const errorDiv = document.getElementById('doctorError');
        if (errorDiv) errorDiv.classList.add('hidden');
    }

    // Open modal
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
            const errorDiv = document.getElementById('doctorError');
            if (errorDiv) errorDiv.classList.add('hidden');
        });
    }

    // ✅ Attach close to BOTH buttons
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    if (closeModalBtn2) {
        closeModalBtn2.addEventListener('click', closeModal);
    }

    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // ========== AJAX FORM SUBMISSION ==========
    doctorForm.addEventListener('submit', function (e) {
        e.preventDefault();

        showSpinner();
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        const formData = new FormData(doctorForm);
         
        // ✅ Combine first_name + last_name into full_name
        const firstName = formData.get('first_name');
        const lastName = formData.get('last_name');
        
        if (firstName && lastName) {
            formData.set('full_name', `${firstName} ${lastName}`);
            // Keep first_name and last_name for potential future use, or remove them:
            formData.delete('first_name');
            formData.delete('last_name');
        }
        fetch("{{ route('doctors.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(async res => {
            let data;

            try {
                data = await res.json();
            } catch (err) {
                throw new Error("Réponse invalide du serveur");
            }

            hideSpinner();
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            if (!res.ok) {
                const errorMsg = data.message || data.detail || "Erreur lors de la création";
                const errorDiv = document.getElementById('doctorError');
                if (errorDiv) {
                    errorDiv.textContent = errorMsg;
                    errorDiv.classList.remove('hidden');
                }
                showToast(errorMsg, 'error');
                return;
            }

            if (data.success && data.doctor) {
                const newOption = new Option(data.doctor.full_name, data.doctor.id, true, true);
                doctorSelect.appendChild(newOption);
                refreshDoctorChoices();

                closeModal();
                showToast(`Le Médecin "${data.doctor.full_name}" créé avec succès`, 'success');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            
            hideSpinner();
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            const errorMsg = err.message || "Erreur de connexion";
            const errorDiv = document.getElementById('doctorError');
            if (errorDiv) {
                errorDiv.textContent = errorMsg;
                errorDiv.classList.remove('hidden');
            }
            showToast(errorMsg, 'error');
        });
    });
});
</script>

{{-- Searchable Doctor Select --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.querySelector('.searchable-select');
    if (doctorSelect) {
        new Choices(doctorSelect, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Choisissez un médecin',
        });
    }
});
</script>


{{-- Phone Popup Script --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('patientForm');
    const phone = document.getElementById('phone');
    const popup = document.getElementById('phonePopup');
    const ignoreBtn = document.getElementById('ignoreBtn');
    const addBtn = document.getElementById('addBtn');

    if (!form || !phone || !popup) return;

    form.addEventListener('submit', function (e) {
        const requiredFields = ['first_name', 'last_name', 'dob', 'gender', 'blood_type'];
        let allFilled = requiredFields.every(id => {
            const el = document.getElementById(id);
            return el && el.value.trim() !== "";
        });

        if (allFilled && phone.value.trim() === "") {
            e.preventDefault();
            popup.classList.remove('hidden');
        }
    });

    if (ignoreBtn) {
        ignoreBtn.addEventListener('click', function () {
            popup.classList.add('hidden');
            form.submit();
        });
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            popup.classList.add('hidden');
            phone.focus();
        });
    }
});
</script>


{{-- QUOTATION FORM SCRIPT --}}
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

    // Discount
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

    // ✅ Recalculate payment-related values
    updatePaymentSummary();
}

    // Update payment summary dynamically
function updatePaymentSummary() {
    const payment = parseFloat(paymentAmountEl.value) || 0;
    const method = paymentMethodEl.value;

    // Net total to pay
    document.getElementById('payment-amount-hidden').value = total;
    receivedDisplay.textContent = `DZD ${formatCurrency(payment)}`;

    // Change calculation (only for cash)
    let change = 0;
    if (method === 'cash') {
        change = Math.max(payment - total, 0);
        changeRow.style.display = 'flex';
        changeDisplay.textContent = `DZD ${formatCurrency(change)}`;
    } else {
        changeRow.style.display = 'none';
    }
    document.getElementById('payment-change-hidden').value = change;

    // ✅ Outstanding balance
    const outstanding = Math.max(total - payment, 0);
    outstandingDisplay.textContent = `DZD ${formatCurrency(outstanding)}`;

    // Optional: hidden input if you need it in backend
    let outstandingHidden = document.getElementById('payment-outstanding-hidden');
    if (outstandingHidden) {
        outstandingHidden.value = outstanding;
    }
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
{{-- JavaScript function for quick patient selection --}}
<script>
function quickSelectPatient(patientId) {
    const select = document.getElementById('patient_id');
    select.value = patientId;
    select.dispatchEvent(new Event('change'));
    
    // Optional: Trigger Alpine.js patient form update
    if (window.Alpine) {
        const patientComponent = Alpine.$data(document.querySelector('[x-data="patientForm()"]'));
        if (patientComponent) {
            patientComponent.showNewPatient = false;
        }
    }
    
    // Optional: Show success feedback
    const row = event.currentTarget;
    row.style.backgroundColor = '#dbeafe';
    setTimeout(() => {
        row.style.backgroundColor = '';
    }, 1500);
}
</script>
@endpush