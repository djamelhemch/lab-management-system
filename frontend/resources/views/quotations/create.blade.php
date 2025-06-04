{{-- resources/views/quotations/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Quotation</h1>
        <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
            Back to Quotations
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6" x-data="quotationForm()">
        <form @submit.prevent="submitForm">
            <!-- Patient Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="patientSearch"
                        @input="searchPatients"
                        @focus="showPatientSuggestions = true"
                        placeholder="Search for existing patient or enter new patient name..."
                        class="w-full p-3 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                        autocomplete="off"
                    >
                    <div x-show="showPatientSuggestions && patientSuggestions.length > 0" 
                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-b-lg shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="patient in patientSuggestions" :key="patient.id">
                            <div @click="selectPatient(patient)" 
                                 class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                                <div class="font-medium" x-text="patient.full_name"></div>
                                <div class="text-sm text-gray-600" x-text="patient.phone"></div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- New Patient Fields -->
                <div x-show="showNewPatientFields" class="mt-4 p-4 bg-gray-50 rounded">
                    <h3 class="text-lg font-medium mb-3">New Patient Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" x-model="newPatient.full_name" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" x-model="newPatient.phone" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" x-model="newPatient.email" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" x-model="newPatient.date_of_birth" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Analysis</label>
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="analysisSearch"
                        @input="searchAnalyses"
                        @focus="showAnalysisSuggestions = true"
                        placeholder="Search and select analyses..."
                        class="w-full p-3 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                        autocomplete="off"
                    >
                    <div x-show="showAnalysisSuggestions && analysisSuggestions.length > 0" 
                         class="absolute z-10 w-full bg-white border border-gray-300 rounded-b-lg shadow-lg max-h-60 overflow-y