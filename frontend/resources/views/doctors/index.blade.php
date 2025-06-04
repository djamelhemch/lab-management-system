@extends('layouts.app')

@section('title', 'Doctors')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Doctors</h2>
    <a href="{{ route('doctors.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
        + Add New Doctor
    </a>
</div>

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if (empty($doctors))
    <div class="text-center text-gray-600">
        <p>No doctors found.</p>
        <a href="{{ route('doctors.create') }}" class="mt-4 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Create Your First Doctor
        </a>
    </div>
@else
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
           <thead class="bg-gray-100 text-sm font-medium text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Specialty</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Prescriber</th>
                    <th class="px-4 py-3 text-left">Patients</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @foreach ($doctors as $doctor)
                    <tr class="hover:bg-gray-50 border-t">
                        <td class="px-4 py-3">
                            <a href="{{ route('doctors.show', $doctor['id']) }}" class="text-red-600 hover:text-red-800 font-medium">
                                {{ $doctor['full_name'] }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $doctor['specialty'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $doctor['phone'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $doctor['email'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 rounded-full text-xs {{ $doctor['is_prescriber'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $doctor['is_prescriber'] ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $doctor['patient_count'] ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <!-- View -->
                                <a href="{{ route('doctors.show', $doctor['id']) }}" 
                                   class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs font-semibold transition">
                                    View
                                </a>
                                <!-- Edit -->
                                <a href="{{ route('doctors.edit', $doctor['id']) }}" 
                                   class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-semibold transition">
                                    Edit
                                </a>
                                <!-- Delete -->
                                <button 
                                    type="button"
                                    onclick="showDeleteModal({{ $doctor['id'] }}, '{{ addslashes($doctor['full_name']) }}')"
                                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs font-semibold transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Confirm Deletion</h3>
        <p class="mb-6 text-gray-700" id="delete-modal-message">
            Are you sure you want to delete this doctor?
        </p>
        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-semibold">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showDeleteModal(doctorId, doctorName) {
        document.getElementById('delete-modal').classList.remove('hidden');
        document.getElementById('delete-form').action = '/doctors/' + doctorId;
        document.getElementById('delete-modal-message').innerHTML = "Are you sure you want to delete <span class='font-bold text-red-600'>" + doctorName + "</span>? This action cannot be undone.";
    }
    function hideDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
    // Optional: Close modal on ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            hideDeleteModal();
        }
    });
</script>
@endsection