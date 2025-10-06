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
        container-id="analyses-table-container"
    />

    <div id="analyses-table-container">
        @include('analyses.partials.table', ['analyses' => $analyses])
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("analysis-search-form");
    const container = document.getElementById("analyses-table-container");
    const route = form.getAttribute("data-table-route");

    function fetchTable() {
        const params = new URLSearchParams(new FormData(form)).toString();

        fetch(route + "?" + params, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        })
        .catch(err => console.error("Error fetching analyses:", err));
    }

    // Run on typing in search
    const searchInput = form.querySelector("[name='q']");
    if (searchInput) {
        searchInput.addEventListener("keyup", fetchTable);
    }

    // Run on category change
    const categorySelect = form.querySelector("[name='category_analyse_id']");
    if (categorySelect) {
        categorySelect.addEventListener("change", fetchTable);
    }
});
</script>
@endpush