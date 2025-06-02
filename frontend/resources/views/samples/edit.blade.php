{{-- resources/views/samples/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Sample')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Edit Sample #{{ $sample['id'] }}</h2>
    <div class="flex space-x-2">
        <a href="{{ route('samples.show', $sample['id']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            ← Back to Sample
        </a>
        <a href="{{ route('samples.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            All Samples
        </a>
    </div>
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
    <form action="{{ route('samples.update', $sample['id']) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Patient and Doctor Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Patient ID *</label>
                <input type="number" 
                       id="patient_id" 
                       name="patient_id" 
                       value="{{ old('patient_id', $sample['patient_id']) }}"
                       required
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter patient ID">
            </div>

            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Doctor ID *</label>
                <input type="number" 
                       id="doctor_id" 
                       name="doctor_id" 
                       value="{{ old('doctor_id', $sample['doctor_id']) }}"
                       required
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter doctor ID">
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
                    <option value="blood" {{ old('sample_type', $sample['sample_type']) == 'blood' ? 'selected' : '' }}>Blood</option>
                    <option value="urine" {{ old('sample_type', $sample['sample_type']) == 'urine' ? 'selected' : '' }}>Urine</option>
                    <option value="pleural_fluid" {{ old('sample_type', $sample['sample_type']) == 'pleural_fluid' ? 'selected' : '' }}>Pleural Fluid</option>
                    <option value="bone_marrow" {{ old('sample_type', $sample['sample_type']) == 'bone_marrow' ? 'selected' : '' }}>Bone Marrow</option>
                    <option value="salts" {{ old('sample_type', $sample['sample_type']) == 'salts' ? 'selected' : '' }}>Salts</option>
                    <option value="pus" {{ old('sample_type', $sample['sample_type']) == 'pus' ? 'selected' : '' }}>Pus</option>
                    <option value="other" {{ old('sample_type', $sample['sample_type']) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label for="tube_type" class="block text-sm font-medium text-gray-700 mb-2">Tube Type</label>
                <select id="tube_type" 
                        name="tube_type"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select tube type</option>
                    <option value="EDTA" {{ old('tube_type', $sample['tube_type']) == 'EDTA' ? 'selected' : '' }}>EDTA</option>
                    <option value="Heparin" {{ old('tube_type', $sample['tube_type']) == 'Heparin' ? 'selected' : '' }}>Heparin</option>
                    <option value="Dry" {{ old('tube_type', $sample['tube_type']) == 'Dry' ? 'selected' : '' }}>Dry</option>
                    <option value="Citrated" {{ old('tube_type', $sample['tube_type']) == 'Citrated' ? 'selected' : '' }}>Citrated</option>
                </select>
            </div>
        </div>

        {{-- Physical Characteristics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="appearance" class="block text-sm font-medium text-gray-700 mb-2">Appearance</label>
                <input type="text" 
                       id="appearance" 
                       name="appearance" 
                       value="{{ old('appearance', $sample['appearance']) }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Clear, Cloudy, Turbid">
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <input type="text" 
                       id="color" 
                       name="color" 
                       value="{{ old('color', $sample['color']) }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., Yellow, Red, Clear">
            </div>

            <div>
                <label for="odor" class="block text-sm font-medium text-gray-700 mb-2">Odor</label>
                <input type="text" 
                       id="odor" 
                       name="odor" 
                       value="{{ old('odor', $sample['odor']) }}"
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
                       value="{{ old('volume_ml', $sample['volume_ml']) }}"
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
                    <option value="pending" {{ old('status', $sample['status']) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="urgent" {{ old('status', $sample['status']) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="in_progress" {{ old('status', $sample['status']) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', $sample['status']) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ old('status', $sample['status']) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label for="assigned_machine_id" class="block text-sm font-medium text-gray-700 mb-2">Assigned Machine ID</label>
                <input type="number" 
                       id="assigned_machine_id" 
                       name="assigned_machine_id" 
                       value="{{ old('assigned_machine_id', $sample['assigned_machine_id']) }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter machine ID">
            </div>
        </div>

        {{-- Collection Date and Barcode --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="collection_date" class="block text-sm font-medium text-gray-700 mb-2">Collection Date</label>
                <input type="datetime-local" 
                       id="collection_date" 
                       name="collection_date" 
                       value="{{ old('collection_date', isset($sample['collection_date']) ? date('Y-m-d\TH:i', strtotime($sample['collection_date'])) : '') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                <input type="text" 
                       id="barcode" 
                       name="barcode" 
                       value="{{ old('barcode', $sample['barcode']) }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Barcode">
            </div>
        </div>

        {{-- Rejection Reason (conditional) --}}
        <div id="rejection_reason_div" class="mb-6" style="{{ old('status', $sample['status']) == 'rejected' ? 'display: block;' : 'display: none;' }}">
            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
            <textarea id="rejection_reason" 
                      name="rejection_reason" 
                      rows="3"
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter rejection reason...">{{ old('rejection_reason', $sample['rejection_reason']) }}</textarea>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('samples.show', $sample['id']) }}" 
               class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Update Sample
            </button>
        </div>
    </form>
</div>

<script>
    // Show/hide rejection reason based on status
    document.getElementById('status').addEventListener('change', function() {
        const rejectionDiv = document.getElementById('rejection_reason_div');
        if (this.value === 'rejected') {
            rejectionDiv.style.display = 'block';
        } else {
            rejectionDiv.style.display = 'none';
        }
    });
</script>
@endsection