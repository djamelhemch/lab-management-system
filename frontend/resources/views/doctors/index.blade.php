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

<x-search-filter
    search-placeholder="Search by name, specialty, phone, email..."
    :search-value="request('q') ?? ''"
    :categories="[]" {{-- No categories for doctors --}}
    :category-value="null"
    category-name=""
    category-label=""
    form-id="doctors-search-form"
    :table-route="route('doctors.table')"
    container-id="analyses-table-container"
/>

<div id="analyses-table-container">
    @include('doctors.partials.table', ['doctors' => $doctors])
</div>


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