@extends('layouts.app')

@section('title', 'Doctor Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-semibold text-gray-800">Doctor Details</h2>
            <p class="text-gray-600">{{ $doctor['full_name'] }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('doctors.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                ← Back to Doctors
            </a>
            <a href="{{ route('doctors.edit', $doctor['id']) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Doctor
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Doctor Information Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Doctor Information</h3>

            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Full Name</label>
                    <p class="text-gray-800">{{ $doctor['full_name'] ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Specialty</label>
                    <p class="text-gray-800">{{ $doctor['specialty'] ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Phone</label>
                    <p class="text-gray-800">{{ $doctor['phone'] ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Email</label>
                    <p class="text-gray-800">{{ $doctor['email'] ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Address</label>
                    <p class="text-gray-800">{{ $doctor['address'] ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Prescriber Status</label>
                    <span class="inline-block px-2 py-1 rounded-full text-xs {{ $doctor['is_prescriber'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $doctor['is_prescriber'] ? 'Yes' : 'No' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Patients Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Patients ({{ count($patients) }})</h3>
                <a href="#" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
                    + Add Patient
                </a>
            </div>

            @if (empty($patients))
                <div class="text-center text-gray-600 py-8">
                    <p>No patients assigned to this doctor.</p>
                    <a href="#" class="mt-2 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Assign First Patient
                    </a>
                </div>
            @else
                <!-- Search Box -->
                <div class="mb-4">
                    <input type="text" id="patient-search" placeholder="Search patients..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Patients Dropdown/List -->
                <div class="space-y-2 max-h-64 overflow-y-auto" id="patients-list">
                    @foreach ($patients as $patient)
                        <div class="patient-item p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                            data-patient-name="{{ strtolower($patient['full_name'] ?? '') }}"
                            data-file-number="{{ strtolower($patient['file_number'] ?? '') }}">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $patient['full_name'] ?? 'N/A' }}</p>
                                    <div class="flex flex-wrap gap-2 text-sm text-gray-600 mt-1">
                                        <span>File: {{ $patient['file_number'] ?? 'N/A' }}</span>
                                        <span>•</span>
                                        <span>{{ $patient['phone'] ?? 'No phone' }}</span>
                                        @if($patient['age'])
                                            <span>•</span>
                                            <span>Age: {{ $patient['age'] }}</span>
                                        @endif
                                        @if($patient['blood_type'])
                                            <span>•</span>
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">{{ $patient['blood_type'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('patients.show', $patient['id']) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                        View Patient
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <!-- Selected Patient Info (Optional) -->
        <div id="selected-patient-info" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
            <h4 class="font-semibold text-blue-800">Selected Patient</h4>
            <p id="selected-patient-details" class="text-blue-700"></p>
        </div>
    </div>
</div>

<script>
// Enhanced search functionality
document.getElementById('patient-search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const patientItems = document.querySelectorAll('.patient-item');

    patientItems.forEach(function(item) {
        const patientName = item.getAttribute('data-patient-name');
        const fileNumber = item.getAttribute('data-file-number');

        if (patientName.includes(searchTerm) || fileNumber.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection