@if(empty($analyses) || (isset($analyses['data']) && count($analyses['data']) === 0) || (is_iterable($analyses) && count($analyses) === 0))
    {{-- Empty state --}}
    <div class="text-center text-gray-600 bg-white p-8 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">No analyses found</h3>
        <p class="mb-4">Get started by creating your first analysis</p>
        <a href="{{ route('analyses.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create New Analysis
        </a>
    </div>
@else
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analyses</h1>
            <p class="text-gray-600">Manage laboratory analyses and their specifications</p>
        </div>
        <a href="{{ route('analyses.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            New Analysis
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sample</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(($analyses['data'] ?? $analyses) as $analysis)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $analysis['code'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $analysis['name'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($analysis['category_analyse'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $analysis['category_analyse']['name'] ?? 'N/A' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                {{ number_format($analysis['price'], 2) }} DZD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $analysis['unit']['name'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $analysis['sample_type']['name'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($analysis['is_active'] ?? true)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('analyses.show', $analysis['id']) }}"
                                       class="text-blue-600 hover:text-blue-900 flex items-center"
                                       title="View">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('analyses.edit', $analysis['id']) }}"
                                       class="text-yellow-600 hover:text-yellow-900 flex items-center"
                                       title="Edit">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('analyses.destroy', $analysis['id']) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this analysis?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 flex items-center"
                                                title="Delete">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($analyses['links']) && count($analyses['links']) > 3)
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-center">
                <div class="pagination flex flex-wrap gap-2">
                    @foreach($analyses['links'] as $link)
                        @if($link['url'])
                            <button 
                                class="px-3 py-1 text-sm rounded-md border 
                                       {{ $link['active'] ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}"
                                data-page="{{ preg_replace('/[^0-9]/', '', $link['url']) }}"
                                onclick="fetchPage('{{ $link['url'] }}')"
                                {!! $link['active'] ? 'disabled' : '' !!}
                            >
                                {!! $link['label'] !!}
                            </button>
                        @else
                            <span class="px-3 py-1 text-sm text-gray-400">{!! $link['label'] !!}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

{{-- AJAX Pagination Script --}}
<script>
function fetchPage(url) {
    if (!url) return;
    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then(r => r.text())
        .then(html => {
            document.getElementById('analyses-table-container').innerHTML = html;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(err => console.error("Pagination fetch error:", err));
}
</script>
