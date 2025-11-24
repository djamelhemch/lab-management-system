@extends('layouts.app')

@section('title', 'Patients')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Patients</h2>
        <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            + Add Patient
        </a>
    </div>

    <x-search-filter
        search-placeholder="Search by name, file number, phone..."
        :search-value="request('q') ?? ''"
        :categories="[]" {{-- No categories for patients --}}
        :category-value="null"
        category-name=""
        category-label=""
        form-id="patients-search-form"
        :table-route="route('patients.table')"
        container-id="analyses-table-container"
    />

    <div id="analyses-table-container">
        @include('patients.partials.table', ['patients' => $patients])
    </div>
@endsection