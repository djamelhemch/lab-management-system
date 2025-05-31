@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Add New Patient</h2>

<form method="POST" action="{{ route('patients.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf

    <input name="first_name" placeholder="First Name" required class="input">
    <input name="last_name" placeholder="Last Name" required class="input">
    <input type="date" name="dob" required class="input">
    <select name="gender" class="input">
        <option value="M">Male</option>
        <option value="F">Female</option>
    </select>
    <input name="phone" placeholder="Phone" class="input">
    <input name="email" placeholder="Email" class="input">
    <input name="address" placeholder="Address" class="input">
    <input name="blood_type" placeholder="Blood Type" class="input">
    <input name="weight" type="number" step="0.1" placeholder="Weight (kg)" class="input">
    <textarea name="allergies" placeholder="Allergies" class="input"></textarea>
    <textarea name="medical_history" placeholder="Medical History" class="input"></textarea>
    <textarea name="chronic_conditions" placeholder="Chronic Conditions" class="input"></textarea>
    <input name="doctor_id" placeholder="Doctor ID" class="input">

    <div class="md:col-span-2">
        <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
            Save Patient
        </button>
    </div>
</form>

<style>
    .input {
        @apply p-2 border border-gray-300 rounded w-full;
    }
</style>
@endsection
