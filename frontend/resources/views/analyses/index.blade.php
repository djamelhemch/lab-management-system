@extends('layouts.app')

@section('content')
<div class="p-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Analyses</h1>
            <p class="text-sm text-gray-600">
                {{ request('show') === 'all'
                    ? 'Showing all analyses (including inactive)'
                    : 'Showing active analyses only' }}
            </p>
        </div>

        <a href="{{ route('analyses.create') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full shadow-sm bg-blue-600 text-white hover:bg-blue-700">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Analysis
        </a>
    </div>

    <x-search-filter
        search-placeholder="Search by name, code, or category..."
        :search-value="request('q')"
        :categories="$categories"
        :category-value="request('category_analyse_id')"
        category-name="category_analyse_id"
        category-label="Category"
        form-id="analysis-search-form"
        :table-route="route('analyses.table')"
        container-id="analyses-table-container"
    />

    <div id="analyses-table-container" class="mt-4">
        @include('analyses.partials.table', [
            'analyses' => $analyses,
            'showAll'  => $showAll ?? (request('show') === 'all')
        ])
    </div>
</div>
@endsection
