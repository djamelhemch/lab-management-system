@extends('layouts.app')

@section('content')
<div class="bg-white shadow p-6 rounded">
    <h2 class="text-2xl font-bold mb-4">Patient Details</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>File #:</strong> {{ $patient['file_number'] }}</p>
        <p><strong>Name:</strong> {{ $patient['first_name'] }} {{ $patient['last_name'] }}</p>
        <p><strong>Date of Birth:</strong> {{ $patient['dob'] }}</p>
        <p><strong>Gender:</strong> {{ $patient['gender'] }}</p>
        <p><strong>Phone:</strong> {{ $patient['phone'] }}</p>
        <p><strong>Email:</strong> {{ $patient['email'] }}</p>
        <p><strong>Address:</strong> {{ $patient['address'] }}</p>
        <p><strong>Blood Type:</strong> {{ $patient['blood_type'] }}</p>
        <p><strong>Weight:</strong> {{ $patient['weight'] }} kg</p>
        <p><strong>Doctor ID:</strong> {{ $patient['doctor_id'] ?? 'N/A' }}</p>
        <p><strong>Allergies:</strong> {{ $patient['allergies'] }}</p>
        <p><strong>Medical History:</strong> {{ $patient['medical_history'] }}</p>
        <p><strong>Chronic Conditions:</strong> {{ $patient['chronic_conditions'] }}</p>
        <p><strong>Created At:</strong> {{ $patient['created_at'] }}</p>
    </div>
</div>
@endsection
