@php
    // Normalize input
    $isPaginated   = isset($analyses['data']) && isset($analyses['meta']);
    $analysesData  = $isPaginated ? $analyses['data'] : $analyses;
    $showAll       = $showAll ?? (request('show') === 'all'); // use passed param first

    // TOTAL is ALWAYS from meta.total (full dataset), never current page
    $totalAnalyses = $analyses['meta']['total'] ?? (is_countable($analysesData) ? count($analysesData) : 0);

    // Active/Inactive are current page only
    $collection    = collect($analysesData);
    $activeCount   = $collection->where('is_active', true)->count();
    $inactiveCount = $collection->count() - $activeCount;

    // Base query for consistent URLs
    $baseQuery = request()->only(['q', 'category_analyse_id']);
@endphp

{{-- Header / summary --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
    <div class="flex gap-4">
        <div class="bg-white rounded-lg shadow p-3 border-l-4 border-blue-500 min-w-[140px]">
            <p class="text-xs font-medium text-gray-600">Total</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($totalAnalyses) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 border-l-4 border-green-500 min-w-[140px]">
            <p class="text-xs font-medium text-gray-600">Active</p>
            <p class="text-xl font-bold text-green-600">{{ $activeCount }}</p>
        </div>
        @if($showAll && $inactiveCount > 0)
            <div class="bg-white rounded-lg shadow p-3 border-l-4 border-red-500 min-w-[140px]">
                <p class="text-xs font-medium text-gray-600">Inactive</p>
                <p class="text-xl font-bold text-red-600">{{ $inactiveCount }}</p>
            </div>
        @endif
    </div>

    {{-- FIXED TOGGLE: always uses analyses.table --}}
    @php
        $toggleParams = array_merge($baseQuery, $showAll ? [] : ['show' => 'all']);
        $toggleUrl = route('analyses.table', $toggleParams);
    @endphp

    <div class="flex items-center gap-3">
        <button
            id="toggleShowAll"
            type="button"
            data-url="{{ $toggleUrl }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full border transition
                   {{ $showAll
                        ? 'bg-gray-800 text-white border-gray-800 hover:bg-gray-900'
                        : 'bg-white text-gray-800 border-gray-300 hover:bg-gray-100 shadow-sm' }}">
            @if($showAll)
                {{-- Hide Inactive: Eye slash (closed eye) --}}
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            @else
                {{-- Show All: Eye (open eye) --}}
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            @endif
            {{ $showAll ? 'Hide Inactive' : 'Show All' }}
        </button>
    </div>
</div>

@if(!$analysesData || (is_countable($analysesData) && count($analysesData) === 0))
    {{-- Empty State --}}
    <div class="bg-white rounded-lg shadow-sm border-2 border-dashed border-gray-300 p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900 mb-2">
            {{ $showAll ? 'No analyses found' : 'No active analyses found' }}
        </h3>
        <p class="text-gray-500 mb-6">
            {{ $showAll
                ? 'Get started by creating your first analysis.'
                : 'There are no active analyses. Try showing all analyses or create a new one.' }}
        </p>
        <div class="flex items-center justify-center gap-3">
            @if(!$showAll)
                <button
                    id="emptyShowAll"
                    type="button"
                    data-url="{{ route('analyses.table', array_merge($baseQuery, ['show' => 'all'])) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Show All Analyses
                </button>
            @endif
            <a href="{{ route('analyses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                Create Analysis
            </a>
        </div>
    </div>
@else
    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sample Types</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($analysesData as $analysis)
                    @php 
                        $isActive = $analysis['is_active'] ?? true;
                        
                        // ✅ Handle multiple sample types (new array structure)
                        $sampleTypes = [];
                        
                        // Try new array structure first
                        if (isset($analysis['sample_types']) && is_array($analysis['sample_types'])) {
                            $sampleTypes = collect($analysis['sample_types'])
                                ->pluck('name')
                                ->filter()
                                ->toArray();
                        }
                        // Fallback to old single sample_type for backward compatibility
                        elseif (isset($analysis['sample_type']['name'])) {
                            $sampleTypes = [$analysis['sample_type']['name']];
                        }
                        
                        $sampleTypesDisplay = !empty($sampleTypes) 
                            ? implode(', ', $sampleTypes) 
                            : 'N/A';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors duration-150 {{ !$isActive ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $analysis['code'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold truncate">
                            {{ $analysis['name'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $analysis['category_analyse']['name'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                            {{ isset($analysis['price']) ? number_format($analysis['price'], 2) . ' DZD' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $analysis['unit']['name'] ?? 'N/A' }}
                        </td>
                        
                        {{-- ✅ FIXED: Display multiple sample types with badges --}}
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if(!empty($sampleTypes))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sampleTypes as $type)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $type }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 italic">N/A</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                         {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <div class="inline-flex items-center space-x-3">
                                <a href="{{ route('analyses.show', $analysis['id']) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                <a href="{{ route('analyses.edit', $analysis['id']) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($analyses['links']) && is_countable($analyses['links']) && count($analyses['links']) > 3)
            <div class="bg-white px-6 py-4 border-t border-gray-200 rounded-b-lg mt-0 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    @if(isset($analyses['meta']))
                        Showing <span class="font-medium">{{ $analyses['meta']['from'] ?? 1 }}</span>
                        to <span class="font-medium">{{ $analyses['meta']['to'] ?? count($analysesData) }}</span>
                        of <span class="font-medium">{{ $analyses['meta']['total'] ?? count($analysesData) }}</span> results
                    @endif
                </div>
                <div class="flex gap-2">
                    @foreach($analyses['links'] as $link)
                        @php
                            $pageUrl = $link['url'];
                            if ($pageUrl) {
                                $query = parse_url($pageUrl, PHP_URL_QUERY);
                                parse_str($query ?? '', $pageParams);
                                $params = array_merge($baseQuery, ['page' => $pageParams['page'] ?? null]);
                                $pageUrl = route('analyses.table', $params);
                            }
                        @endphp

                        @if($pageUrl)
                            <button
                                type="button"
                                class="px-3 py-2 text-sm rounded-md border transition-all duration-150
                                       {{ $link['active'] ? 'bg-blue-600 text-white border-blue-600 cursor-default' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                                onclick="fetchAnalysesTable('{{ $pageUrl }}')"
                                {!! $link['active'] ? 'disabled' : '' !!}>
                                {!! $link['label'] !!}
                            </button>
                        @else
                            <span class="px-3 py-2 text-sm text-gray-400 border border-gray-200 rounded-md bg-gray-50">
                                {!! $link['label'] !!}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

<script>
(function() {
    // Global state
    let currentSort = { column: null, direction: 'asc' };

    // Main AJAX fetch function
    window.fetchAnalysesTable = function(url) {
        fetch(url, { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.text();
        })
        .then(html => {
            const container = document.getElementById('analyses-table-container');
            if (container) {
                container.innerHTML = html;
                initEventListeners(); // Re-bind events after reload
            }
        })
        .catch(err => {
            console.error('AJAX Error:', err);
            alert('Failed to load data. Please refresh the page.');
        });
    };

    // Event delegation - works even after AJAX reload
    function initEventListeners() {
        // Toggle button
        document.querySelector('#toggleShowAll')?.addEventListener('click', function(e) {
            e.preventDefault();
            fetchAnalysesTable(this.dataset.url);
        });

        // Empty state button
        document.querySelector('#emptyShowAll')?.addEventListener('click', function(e) {
            e.preventDefault();
            fetchAnalysesTable(this.dataset.url);
        });
    }

    // Sorting function
    window.sortTable = function(column) {
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }

        const url = new URL(window.location);
        url.searchParams.set('sort', column);
        url.searchParams.set('direction', currentSort.direction);
        fetchAnalysesTable(url.pathname + '?' + url.searchParams);
    };

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEventListeners);
    } else {
        initEventListeners();
    }

})();
</script>
