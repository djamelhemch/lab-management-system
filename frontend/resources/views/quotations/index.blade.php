{{-- resources/views/quotations/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Quotations</h1>
        <a href="{{ route('quotations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            New Quotation
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <x-search-filter
        search-placeholder="Search quotations..."
        :search-value="request('q') ?? ''"
        :categories="[
            ['id' => 'draft', 'name' => 'Draft'],
            ['id' => 'confirmed', 'name' => 'Confirmed'],
            ['id' => 'converted', 'name' => 'Converted']
        ]"
        :category-value="request('status') ?? ''"
        category-name="status"
        category-label="Status"
        form-id="quotations-search-form"
        :table-route="route('quotations.table')"
        container-id="quotations-table-container"
    />

    <div id="quotations-table-container">
        @include('quotations.partials.table', ['quotations' => $quotations])
    </div>
</div>
@endsection