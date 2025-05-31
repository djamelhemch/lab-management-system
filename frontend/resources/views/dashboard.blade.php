@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-shadow flex items-center space-x-5">
        <i class="fas fa-users text-[#bc1622] text-4xl"></i>
        <div>
            <div class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Total Patients</div>
            <div class="text-3xl font-extrabold text-gray-900">{{ $patientsCount }}</div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-shadow flex items-center space-x-5">
        <i class="fas fa-user-md text-[#bc1622] text-4xl"></i>
        <div>
            <div class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Total Doctors</div>
            <div class="text-3xl font-extrabold text-gray-900">{{ $doctorsCount }}</div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-shadow flex items-center space-x-5">
        <i class="fas fa-vials text-[#bc1622] text-4xl"></i>
        <div>
            <div class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Samples Today</div>
            <div class="text-3xl font-extrabold text-gray-900">{{ $samplesToday }}</div>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow">
    <h3 class="text-xl font-semibold text-[#bc1622] mb-3">Welcome to Abdelatif Lab</h3>
    <p class="text-gray-600 leading-relaxed">
        Use the sidebar to manage patients, doctors, and laboratory operations.
    </p>
</div>
@endsection
