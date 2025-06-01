@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Patient</h2>
    <form method="POST" action="{{ route('patients.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <input name="first_name" placeholder="First Name" required class="input" />
        <input name="last_name" placeholder="Last Name" required class="input" />
        <input type="date" name="dob" required class="input" />
        <select name="gender" class="input" required>
            <option value="">Gender</option>
            <option value="M">Male</option>
            <option value="F">Female</option>
        </select>
        <input name="phone" placeholder="Phone" class="input" />
        <input name="email" placeholder="Email" class="input" />
        <input name="address" placeholder="Address" class="input" />
        <input name="blood_type" placeholder="Blood Type" class="input" />
        <input name="weight" type="number" step="0.1" placeholder="Weight (kg)" class="input" />
        <textarea name="allergies" placeholder="Allergies" class="input"></textarea>
        <textarea name="medical_history" placeholder="Medical History" class="input"></textarea>
        <textarea name="chronic_conditions" placeholder="Chronic Conditions" class="input"></textarea>

        <div class="md:col-span-2">
            <label class="block mb-1 font-semibold text-gray-700">Doctor</label>
            <select name="doctor_id" class="input" x-data x-init="$el._choices = new Choices($el, {searchEnabled: true, itemSelectText: ''})">
                <option value="">Select Doctor</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor['id'] }}">{{ $doctor['full_name'] }} ({{ $doctor['specialty'] ?? 'N/A' }})</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <button type="submit" class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 font-semibold transition">
                Save Patient
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