@extends('layouts.app')

@section('content')
<div class="bg-white shadow p-6 rounded">
    <h2 class="text-2xl font-bold mb-4">Patient Details</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Dossier N°:</strong> {{ $patient['file_number'] }}</p>
        <p><strong>Nom Prénom:</strong> {{ $patient['first_name'] }} {{ $patient['last_name'] }}</p>
        <p><strong>Age:</strong> {{ $patient['age'] }} ans</p>
        <p><strong>Gender:</strong> {{ $patient['gender'] }}</p>
        <p><strong>Phone:</strong> {{ $patient['phone'] }}</p>
        <p><strong>Email:</strong> {{ $patient['email'] }}</p>
        <p><strong>Address:</strong> {{ $patient['address'] }}</p>
        <p><strong>Blood Type:</strong> {{ $patient['blood_type'] }}</p>
        <p><strong>Weight:</strong> {{ $patient['weight'] }} kg</p>
        <p>
            <a href="{{ route('doctors.show', $patient['doctor_id']) }}"   
            class="text-red-600 hover:text-red-800 hover:underline font-medium">
            <strong>Doctor:</strong> {{ $patient['doctor_full_name'] ?? 'N/A' }}
            </a>
        </p>
        <p><strong>Allergies:</strong> {{ $patient['allergies'] }}</p>
        <p><strong>Medical History:</strong> {{ $patient['medical_history'] }}</p>
        <p><strong>Chronic Conditions:</strong> {{ $patient['chronic_conditions'] }}</p>
        <p><strong>Created At:</strong> {{ $patient['created_at'] }}</p>
    </div>

    <a href="{{ route('patients.edit', $patient['id']) }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Edit Patient
    </a>
</div>
@endsection