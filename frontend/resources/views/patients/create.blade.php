@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-8">Ajouter un Patient</h2>

    <form method="POST" action="{{ route('patients.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
            <label for="blood_type" class="block mb-1 font-semibold text-gray-700">Groupe Sanguin</label>
            <select id="blood_type" name="blood_type"
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
            <select id="doctor_id" name="doctor_id" class="p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition searchable-select">
                <option value="" disabled selected>Choisissez un médecin</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor['id'] }}" {{ old('doctor_id') == $doctor['id'] ? 'selected' : '' }}>
                        {{ $doctor['full_name'] }}
                    </option>
                @endforeach
            </select>
            @error('doctor_id') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
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
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

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
