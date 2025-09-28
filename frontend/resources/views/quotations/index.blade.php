@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    {{-- Page Header --}}
    <div class="container mx-auto px-4 mb-8">
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center">
                        {{-- Clipboard Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2h6a2 2 0 012 2v2h-2V4H9v2H7V4a2 2 0 012-2zM7 8h10v12a2 2 0 01-2 2H9a2 2 0 01-2-2V8z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Quotations Management</h1>
                        <p class="text-gray-600">Manage and track all laboratory quotations</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('quotations.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium shadow-sm transition-all">
                        {{-- Plus Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Quotation
                    </a>
                    <a href="{{ route('agreements.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium shadow-sm transition-all">
                        {{-- Document Text Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 6h8m-6 16h10a2 2 0 002-2V8a2 2 0 00-.586-1.414l-4-4A2 2 0 0013.586 2H6a2 2 0 00-2 2v2" />
                        </svg>
                        Manage Agreements
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                {{-- Check Circle Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-green-800">Success</p>
                <p class="text-green-700 text-sm">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                {{-- Exclamation Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-red-800">Error</p>
                <p class="text-red-700 text-sm">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Search and Filters --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                {{-- Search Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-800">Search & Filter</h2>
            </div>
            
            <x-search-filter
                search-placeholder="Search quotations by patient name, ID, or file number..."
                :search-value="request('q') ?? ''"
                :categories="[
                    ['id' => 'draft', 'name' => '📝 Draft'],
                    ['id' => 'confirmed', 'name' => '✅ Confirmed'],
                    ['id' => 'converted', 'name' => '🔄 Converted']
                ]"
                :category-value="request('status') ?? ''"
                category-name="status"
                category-label="Status Filter"
                form-id="quotations-search-form"
                :table-route="route('quotations.table')"
                container-id="quotations-table-container"
            />
        </div>

        {{-- Data Table --}}
        <div id="quotations-table-container">
            @include('quotations.partials.table', ['quotations' => $quotations])
        </div>
    </div>
</div>
@endsection
