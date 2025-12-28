@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-8">Ajouter un Patient</h2>

    <form id="patientForm" method="POST" action="{{ route('patients.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        {{-- First Name --}}
        <div>
            <label for="first_name" class="block mb-1 font-semibold text-gray-700">Prénom <span class="text-red-500">*</span></label>
            <input id="first_name" name="first_name" type="text" placeholder="Prénom" value="{{ old('first_name') }}" required
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('first_name') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Last Name --}}
        <div>
            <label for="last_name" class="block mb-1 font-semibold text-gray-700">Nom <span class="text-red-500">*</span></label>
            <input id="last_name" name="last_name" type="text" placeholder="Nom" value="{{ old('last_name') }}" required
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('last_name') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Date of Birth --}}
        <div>
            <label for="dob" class="block mb-1 font-semibold text-gray-700">Date de naissance <span class="text-red-500">*</span></label>
            <input id="dob" name="dob" type="date" value="{{ old('dob') }}" required
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('dob') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Gender --}}
        <div>
            <label for="gender" class="block mb-1 font-semibold text-gray-700">Sexe <span class="text-red-500">*</span></label>
            <select id="gender" name="gender" required
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition">
                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Sélectionnez le sexe</option>
                <option value="H" {{ old('gender') == 'H' ? 'selected' : '' }}>Homme</option>
                <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Femme</option>
            </select>
            @error('gender') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="block mb-1 font-semibold text-gray-700">Téléphone</label>
            <input id="phone" name="phone" type="tel" placeholder="Ex: +212 600 000 000" value="{{ old('phone') }}"
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('phone') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block mb-1 font-semibold text-gray-700">Email</label>
            <input id="email" name="email" type="email" placeholder="exemple@domaine.com" value="{{ old('email') }}"
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('email') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Address --}}
        <div class="md:col-span-2">
            <label for="address" class="block mb-1 font-semibold text-gray-700">Adresse</label>
            <input id="address" name="address" type="text" placeholder="Adresse complète" value="{{ old('address') }}"
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('address') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Blood Type --}}
        <div>
            <label for="blood_type" class="block mb-1 font-semibold text-gray-700">Groupe Sanguin <span class="text-red-500">*</span></label>
            <select id="blood_type" name="blood_type" required
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition">
                <option value="" disabled {{ old('blood_type') ? '' : 'selected' }}>Sélectionnez un groupe sanguin</option>
                <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
            @error('blood_type') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Weight --}}
        <div>
            <label for="weight" class="block mb-1 font-semibold text-gray-700">Poids (kg)</label>
            <input id="weight" name="weight" type="number" step="0.1" placeholder="Poids en kg" value="{{ old('weight') }}"
                class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition" />
            @error('weight') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Allergies --}}
        <div class="md:col-span-2">
            <label for="allergies" class="block mb-1 font-semibold text-gray-700">Allergies</label>
            <textarea id="allergies" name="allergies" rows="3" placeholder="Décrivez les allergies" class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition resize-y">{{ old('allergies') }}</textarea>
            @error('allergies') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Medical History --}}
        <div class="md:col-span-2">
            <label for="medical_history" class="block mb-1 font-semibold text-gray-700">Historique Médical</label>
            <textarea id="medical_history" name="medical_history" rows="3" placeholder="Décrivez l'historique médical" class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition resize-y">{{ old('medical_history') }}</textarea>
            @error('medical_history') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Chronic Conditions --}}
        <div class="md:col-span-2">
            <label for="chronic_conditions" class="block mb-1 font-semibold text-gray-700">Maladies Chroniques</label>
            <textarea id="chronic_conditions" name="chronic_conditions" rows="3" placeholder="Décrivez les maladies chroniques" class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition resize-y">{{ old('chronic_conditions') }}</textarea>
            @error('chronic_conditions') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
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
        {{-- Submit Button --}}
        <div class="md:col-span-2">
            <button type="submit" 
                class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-shadow shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Créer Patient
            </button>
        </div>
    </form>
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
@endpush

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
