@extends('layouts.app')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-semibold mb-4">Analyses</h1>

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

    <div id="analyses-table-container">
        @include('analyses.partials.table', ['analyses' => $analyses])
    </div>
</div>
@endsection
