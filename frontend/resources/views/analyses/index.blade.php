{{-- resources/views/analyses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laboratory Analyses')

@section('content')
    <x-search-filter
        search-placeholder="Search by name, code, category..."
        :search-value="request('q')"
        :categories="$categories"
        :category-value="request('category_analyse_id')"
        category-name="category_analyse_id"
        category-label="Category"
        form-id="analysis-search-form"
        :table-route="route('analyses.table')"
    />

    <div id="analyses-table-container">
        @include('analyses.partials.table', ['analyses' => $analyses])
    </div>
@endsection
