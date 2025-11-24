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
            {{-- Patient --}}
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
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

            {{-- Doctor --}}
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Doctor *</label>
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

        {{-- Physical Characteristics --}}
        <div>
            <label for="appearance" class="block text-sm font-medium text-gray-700 mb-2">Appearance</label>
            <input type="text" name="appearance" id="appearance" value="{{ old('appearance') }}" placeholder="e.g., Clear, Cloudy, Turbid"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Volume, Status, and Machine --}}
        <div>
            <label for="volume_ml" class="block text-sm font-medium text-gray-700 mb-2">Volume (ml)</label>
            <input 
                type="number" 
                id="volume_ml" 
                name="volume_ml" 
                value="{{ old('volume_ml') }}" 
                placeholder="Enter volume in ml" 
                step="0.1" 
                min="0"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
        </div>


            {{-- Assigned Machine --}}
            <div>
                <label for="device_id" class="block text-sm font-medium text-gray-700 mb-2">Assigned Machine *</label>
                <select id="device_id" 
                        name="device_id"
                        required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a device...</option>
                    @foreach($devices as $device)
                        <option value="{{ $device['id'] }}" {{ old('device_id') == $device['id'] ? 'selected' : '' }}>
                            {{ $device['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Collection Date & Barcode --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Collection Date -->
    <div>
        <label for="collection_date" class="block text-sm font-medium text-gray-700 mb-2">Collection Date</label>
        <input 
            type="datetime-local" 
            id="collection_date" 
            name="collection_date" 
            value="{{ old('collection_date') ?? now()->format('Y-m-d\TH:i') }}" 
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
        />
    </div>

    <!-- Barcode -->
    <div>
        <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Barcode (Optional)</label>
        <input 
            type="text" 
            id="barcode" 
            name="barcode" 
            value="{{ old('barcode') }}" 
            placeholder="Leave empty for auto-generation"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
        />
    </div>
</div>


        {{-- Rejection Reason --}}
        <div id="rejection_reason_div" class="mb-6 hidden">
            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
            <textarea id="rejection_reason" 
                      name="rejection_reason" 
                      rows="3"
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter rejection reason...">{{ old('rejection_reason') }}</textarea>
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('samples.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Create Sample</button>
        </div>
    </form>
</div>

{{-- Select2 & Scripts --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function() {
    $('#patient_id, #doctor_id').select2({
        placeholder: 'Search...',
        allowClear: true,
        ajax: {
            url: function() {
                return this[0].id === 'patient_id'
                    ? '{{ route("api.search.patients") }}'
                    : '{{ route("api.search.doctors") }}';
            },
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id,
                    text: item.first_name ? `${item.first_name} ${item.last_name}` : item.name
                }))
            }),
            cache: true
        }
    });

    $('#status').on('change', function() {
        $('#rejection_reason_div').toggle(this.value === 'rejected');
    });

    const collectionInput = document.getElementById('collection_date');
    if (!collectionInput.value) {
        const now = new Date();
        const localDateTime = new Date(now - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        collectionInput.value = localDateTime;
    }
});
</script>
@endsection
