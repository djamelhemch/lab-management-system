<div>
    <form
        id="{{ $formId }}"
        class="mb-6 bg-white p-4 rounded-lg shadow"
        onsubmit="return false;"
        data-table-route="{{ $tableRoute }}"
    >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search Input -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="text"
                    id="search-input"
                    name="q"
                    value="{{ $searchValue }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 pr-10"
                >
                <button
                    type="button"
                    id="clear-search"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 flex items-center justify-center text-gray-500 hover:text-gray-700 focus:outline-none rounded-full h-6 w-6"
                    style="display: none;"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Category Filter -->
            @if(!empty($categoryName) && !empty($categoryLabel) && !empty($categories))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $categoryLabel }}</label>
                    <select
                        id="category-filter"
                        name="{{ $categoryName }}"
                        class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All {{ $categoryLabel }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ $categoryValue == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
        </div>
            {{-- REQUIRED FOR SORT --}}
        <input type="hidden" name="sort_by" value="{{ request('sort_by', '') }}">
        <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

    </form>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="hidden mb-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-blue-600">Searching...</span>
            </div>
        </div>
    </div>
</div>

<style>
#clear-search {
    right: 0.5rem;
    top: 65%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.25rem;
    height: 1.25rem;
    padding: 0;
    border-radius: 9999px;
    background-color: rgba(229, 231, 235, 0.6);
    color: #4b5563;
}
#clear-search:hover {
    background-color: rgba(209, 213, 219, 0.7);
    color: #374151;
}
#clear-search svg {
    width: 0.75rem;
    height: 0.75rem;
}
</style>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById(@json($formId));
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const loadingIndicator = document.getElementById('loading-indicator');
    const clearSearch = document.getElementById('clear-search');
    const container = document.getElementById(@json($containerId));
    const route = form.getAttribute("data-table-route");

    if (!form || !route || !container) {
        console.error("Search filter misconfigured. Check formId, containerId, and tableRoute.");
        return;
    }

    let debounceTimeout = null;
    let currentRequest = null;

    function showLoading() {
        loadingIndicator.classList.remove('hidden');
    }
    function hideLoading() {
        loadingIndicator.classList.add('hidden');
    }

    function fetchTable(extra = {}) {
    if (currentRequest) currentRequest.abort();

    const formData = new FormData(form);

    // Override / inject extra params (page, sort, etc.)
    Object.entries(extra).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            formData.set(key, value);
        }
    });

    const params = new URLSearchParams(formData);

    showLoading();

    const controller = new AbortController();
    currentRequest = controller;

    fetch(`${route}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        },
        signal: controller.signal
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP error: ${res.status}`);
        return res.text();
    })
    .then(html => {
        container.innerHTML = html;
        hideLoading();
        currentRequest = null;
    })
    .catch(err => {
        if (err.name !== 'AbortError') {
            console.error(err);
        }
        hideLoading();
        currentRequest = null;
    });
}


    // Debounced live search
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(fetchTable, 300);
        clearSearch.style.display = searchInput.value.length > 0 ? 'flex' : 'none';
    });

    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => {
            clearTimeout(debounceTimeout);
            fetchTable();
        });
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        clearTimeout(debounceTimeout);
        fetchTable();
    });

    clearSearch.addEventListener('click', () => {
        searchInput.value = '';
        clearSearch.style.display = 'none';
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(fetchTable, 100);
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            searchInput.value = '';
            clearSearch.style.display = 'none';
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(fetchTable, 100);
        }
    });

    clearSearch.style.display = searchInput.value.length > 0 ? 'flex' : 'none';
});
</script>
@endpush
