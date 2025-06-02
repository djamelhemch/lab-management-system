{{-- resources/views/samples/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Sample')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Add New Sample</h2>
    <a href="{{ route('samples.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
        ← Back to Samples
    </a>
</div>

{{-- Error Messages --}}
@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('samples.store') }}" method="POST">
        @csrf
        
        {{-- Patient and Doctor Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
                <div class="relative">
                    <select id="patient_id" 
                            name="patient_id" 
                            required
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 searchable-select">
                        <option value="">Select a patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient['id'] }}" {{ old('patient_id') == $patient['id'] ? 'selected' : '' }}>
                                {{ $patient['first_name'] ?? '' }} {{ $patient['last_name'] ?? '' }} 
                                (ID: {{ $patient['id'] }})
                                @if(isset($patient['file_number']))
                                    - File: {{ $patient['file_number'] }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Doctor *</label>
                <div class="relative">
                    <select id="doctor_id" 
                            name="doctor_id" 
                            required
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 searchable-select">
                        <option value="">Select a doctor...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor['id'] }}" {{ old('doctor_id') == $doctor['id'] ? 'selected' : '' }}>
                                Dr. {{ $doctor['first_name'] ?? '' }} {{ $doctor['last_name'] ?? '' }}
                                @if(isset($doctor['specialization']))
                                    - {{ $doctor['specialization'] }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Rest of your form fields remain the same --}}
        {{-- Sample Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="sample_type" class="block text-sm font-medium text-gray-700 mb-2">Sample Type *</label>
                <select id="sample_type" 
                        name="sample_type" 
                        required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select sample type</option>
                    <option value="blood" {{ old('sample_type') == 'blood' ? 'selected' : '' }}>Blood</option>
                    <option value="urine" {{ old('sample_type') == 'urine' ? 'selected' : '' }}>Urine</option>
                    <option value="pleural_fluid" {{ old('sample_type') == 'pleural_fluid' ? 'selected' : '' }}>Pleural Fluid</option>
                    <option value="bone_marrow" {{ old('sample_type') == 'bone_marrow' ? 'selected' : '' }}>Bone Marrow</option>
                    <option value="salts" {{ old('sample_type') == 'salts' ? 'selected' : '' }}>Salts</option>
                    <option value="pus" {{ old('sample_type') == 'pus' ? 'selected' : '' }}>Pus</option>
                    <option value="other" {{ old('sample_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label for="tube_type" class="block text-sm font-medium text-gray-700 mb-2">Tube Type</label>
                <select id="tube_type" 
                        name="tube_type"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select tube type</option>
                    <option value="EDTA" {{ old('tube_type') == 'EDTA' ? 'selected' : '' }}>EDTA</option>
                    <option value="Heparin" {{ old('tube_type') == 'Heparin' ? 'selected' : '' }}>Heparin</option>
                    <option value="Dry" {{ old('tube_type') == 'Dry' ? 'selected' : '' }}>Dry</option>
                    <option value="Citrated" {{ old('tube_type') == 'Citrated' ? 'selected' : '' }}>Citrated</option>
                </select>
            </div>
        </div>

        {{-- Continue with the rest of your form fields... --}}
        {{-- Physical Characteristics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="appearance" class="block text-sm font-medium text-gray-700 mb-2">Appearance</label>
                <input type="text" 
                       id="appearance" 
                       name="appearance" 
                       value="{{ old('appearance') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Clear, Cloudy, Turbid">
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <input type="text" 
                       id="color" 
                       name="color" 
                       value="{{ old('color') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Yellow, Red, Clear">
            </div>

            <div>
                <label for="odor" class="block text-sm font-medium text-gray-700 mb-2">Odor</label>
                <input type="text" 
                       id="odor" 
                       name="odor" 
                       value="{{ old('odor') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Normal, Strong, None">
            </div>
        </div>

        {{-- Volume and Status --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="volume_ml" class="block text-sm font-medium text-gray-700 mb-2">Volume (ml)</label>
                <input type="number" 
                       id="volume_ml" 
                       name="volume_ml" 
                       value="{{ old('volume_ml') }}"
                       step="0.1"
                       min="0"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter volume in ml">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" 
                        name="status"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="urgent" {{ old('status') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label for="assigned_machine_id" class="block text-sm font-medium text-gray-700 mb-2">Assigned Machine ID</label>
                <input type="number" 
                       id="assigned_machine_id" 
                       name="assigned_machine_id" 
                       value="{{ old('assigned_machine_id') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter machine ID">
            </div>
        </div>

        {{-- Collection Date --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="collection_date" class="block text-sm font-medium text-gray-700 mb-2">Collection Date</label>
                <input type="datetime-local" 
                       id="collection_date" 
                       name="collection_date" 
                       value="{{ old('collection_date') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Barcode (Optional)</label>
                <input type="text" 
                       id="barcode" 
                       name="barcode" 
                       value="{{ old('barcode') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Leave empty for auto-generation">
            </div>
        </div>

        {{-- Rejection Reason (conditional) --}}
        <div id="rejection_reason_div" class="mb-6" style="display: none;">
            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
            <textarea id="rejection_reason" 
                      name="rejection_reason" 
                      rows="3"
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter rejection reason...">{{ old('rejection_reason') }}</textarea>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('samples.index') }}" 
               class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Create Sample
            </button>
        </div>
    </form>
</div>

{{-- Include Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- Include Select2 JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for patients
    $('#patient_id').select2({
        placeholder: 'Search for a patient...',
        allowClear: true,
        ajax: {
            url: '{{ route("api.search.patients") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(patient) {
                        return {
                            id: patient.id,
                            text: `${patient.first_name || ''} ${patient.last_name || ''} (ID: ${patient.id})${patient.file_number ? ' - File: ' + patient.file_number : ''}`
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Initialize Select2 for doctors
    $('#doctor_id').select2({
        placeholder: 'Search for a doctor...',
        allowClear: true,
        ajax: {
            url: '{{ route("api.search.doctors") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(doctor) {
                        return {
                            id: doctor.id,
                            text: `Dr. ${doctor.first_name || ''} ${doctor.last_name || ''}${doctor.specialization ? ' - ' + doctor.specialization : ''}`
                        };
                    })
                };
            },
            cache: true
        }
    });
});

// Show/hide rejection reason based on status
document.getElementById('status').addEventListener('change', function() {
    const rejectionDiv = document.getElementById('rejection_reason_div');
    if (this.value === 'rejected') {
        rejectionDiv.style.display = 'block';
    } else {
        rejectionDiv.style.display = 'none';
    }
});

// Set default collection date to now
document.addEventListener('DOMContentLoaded', function() {
    const collectionDateInput = document.getElementById('collection_date');
    if (!collectionDateInput.value) {
        const now = new Date();
        const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        collectionDateInput.value = localDateTime;
    }
});
</script>
@endsection