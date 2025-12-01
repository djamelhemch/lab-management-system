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

<!-- Create Doctor Modal -->
<div id="doctorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Nouveau Médecin</h3>
            <button type="button" id="closeDoctorModal" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="doctorForm" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                <input type="text" name="full_name" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                    placeholder="Dr. Jean Dupont"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Spécialité</label>
                <input type="text" name="specialty" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" 
                    placeholder="Cardiologue"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="phone" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" 
                    placeholder="+213 659 18 09 59"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" 
                    placeholder="docteur@example.com"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <input type="text" name="address" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" 
                    placeholder="123 Rue de la Santé"/>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input id="is_prescriber" name="is_prescriber" type="checkbox" 
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_prescriber" class="text-sm font-medium text-gray-700">Peut prescrire des ordonnances</label>
            </div>

            {{-- Error Display --}}
            <div id="doctorError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="closeDoctorModal"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    Annuler
                </button>

                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg transition-all hover:shadow-xl">
                    Créer le médecin
                </button>
            </div>
        </form>
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
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 10);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ========== SPINNER CONTROLS ==========
    const spinnerOverlay = document.getElementById('spinnerOverlay');
    
    function showSpinner() {
        spinnerOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
    
    function hideSpinner() {
        spinnerOverlay.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // ========== MODAL & FORM LOGIC ==========
    const modal = document.getElementById('doctorModal');
    const openModalBtn = document.getElementById('openDoctorModal');
    const closeModalBtn = document.getElementById('closeDoctorModal');
    const doctorForm = document.getElementById('doctorForm');
    const doctorSelect = document.getElementById('doctor_id');
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

    openModalBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        document.getElementById('doctorError').classList.add('hidden');
    });
    
    closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));

    // ========== AJAX FORM SUBMISSION ==========
    doctorForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Show spinner & disable submit button
        showSpinner();
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

        const formData = new FormData(doctorForm);

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

            // Hide spinner
            hideSpinner();
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

            if (!res.ok) {
                // Show error in modal
                const errorMsg = data.message || data.detail || "Erreur lors de la création";
                document.getElementById('doctorError').textContent = errorMsg;
                document.getElementById('doctorError').classList.remove('hidden');
                
                // Also show error toast
                showToast(errorMsg, 'error');
                return;
            }

            // SUCCESS!
            if (data.success && data.doctor) {
                // Add new doctor to select
                const newOption = new Option(data.doctor.full_name, data.doctor.id, true, true);
                doctorSelect.appendChild(newOption);
                refreshDoctorChoices();

                // Close modal & reset form
                modal.classList.add('hidden');
                doctorForm.reset();
                document.getElementById('doctorError').classList.add('hidden');

                // Show success toast
                showToast(`✓ Médecin "${data.doctor.full_name}" créé avec succès`, 'success');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            
            // Hide spinner
            hideSpinner();
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

            // Show error
            const errorMsg = err.message || "Erreur de connexion";
            document.getElementById('doctorError').textContent = errorMsg;
            document.getElementById('doctorError').classList.remove('hidden');
            showToast(errorMsg, 'error');
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const doctorSelect = document.querySelector('.searchable-select');
        if(doctorSelect) {
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('patientForm');
    const phone = document.getElementById('phone');
    const popup = document.getElementById('phonePopup');
    const ignoreBtn = document.getElementById('ignoreBtn');
    const addBtn = document.getElementById('addBtn');

    form.addEventListener('submit', function (e) {
        const requiredFields = ['first_name', 'last_name', 'dob', 'gender'];
        let allFilled = requiredFields.every(id => document.getElementById(id).value.trim() !== "");

        if (allFilled && phone.value.trim() === "") {
            e.preventDefault(); // stop submission
            popup.classList.remove('hidden');
        }
    });

    ignoreBtn.addEventListener('click', function () {
        popup.classList.add('hidden');
        form.submit(); // submit normally
    });

    addBtn.addEventListener('click', function () {
        popup.classList.add('hidden');
        phone.focus();
    });
});
</script>