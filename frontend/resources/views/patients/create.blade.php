@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajouter un Patient</h2>
    <form method="POST" action="{{ route('patients.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <input name="first_name" placeholder="Prénom" required class="input" />
        <input name="last_name" placeholder="Nom" required class="input" />
        <input type="date" name="dob" required class="input" />
        <select name="gender" class="input" required>
            <option value="">Sexe</option>
            <option value="H">Homme</option>
            <option value="F">Femme</option>
        </select>
        <input name="phone" placeholder="Tél" class="input" />
        <input name="email" placeholder="Email" class="input" />
        <input name="address" placeholder="Address" class="input" />
        <input name="blood_type" placeholder="Groupe Sanguin" class="input" />
        <input name="weight" type="number" step="0.1" placeholder="Weight (kg)" class="input" />
        <textarea name="allergies" placeholder="Allergies" class="input"></textarea>
        <textarea name="medical_history" placeholder="Historique Médical" class="input"></textarea>
        <textarea name="chronic_conditions" placeholder="Maladies Chroniques" class="input"></textarea>

        <div class="md:col-span-2">
            <label class="block mb-1 font-semibold text-gray-700">Médecin Traitant</label>
            <select name="doctor_id" class="input">
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor['id'] }}" {{ old('doctor_id') == $doctor['id'] ? 'selected' : '' }}>
                        {{ $doctor['full_name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <button type="submit" class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 font-semibold transition">
                Créer Patient
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Choices.js for searchable dropdowns -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@endpush

<style>
.input {
    @apply p-3 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-red-200 focus:border-red-400 transition;
}
</style>