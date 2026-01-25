<div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
    {{-- Component Header --}}
    <div class="bg-gradient-to-r from-indigo-50 via-white to-white p-6 border-b border-gray-100 relative">
    <div class="absolute left-0 top-0 h-full w-1 bg-indigo-600"></div>

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Aperçu des visites et devis</h3>
        </div>


            <div class="text-sm font-medium text-gray-600 bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                @php
                    $quotationsCount = is_array($quotations['items'] ?? []) ? count($quotations['items']) : 0;
                @endphp
                {{ $quotationsCount }} {{ Str::plural('result', $quotationsCount) }}
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-b from-gray-50 to-white text-xs uppercase tracking-wider text-gray-500 sticky top-0 z-10 shadow-sm">
            <tr>
                {{-- ID --}}
                <th class="px-6 py-4 text-left font-semibold text-gray-700">#</th>
                
               {{-- Patient - CLICKABLE --}}
                <th class="px-6 py-4 text-left">
                    <button type="button"
                            class="sort-btn group flex items-center justify-between w-full
                                font-semibold text-gray-700 hover:text-indigo-700
                                bg-gray-50 hover:bg-indigo-50
                                border border-transparent hover:border-indigo-300
                                rounded-xl px-3 py-2 shadow-sm hover:shadow-md
                                transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            data-sort="name">

                        <span class="flex items-center gap-2">
                            Patient
                            {{-- Vertical arrows --}}
                            <span class="flex flex-col justify-center text-gray-400 group-hover:text-indigo-500">
                                {{-- Up arrow --}}
                                <span class="text-[0.55rem] leading-none {{ request('sort_by') === 'name' && request('sort_dir','desc') === 'asc' ? 'text-indigo-600 font-bold' : '' }}">
                                    ▲
                                </span>
                                {{-- Down arrow --}}
                                <span class="text-[0.55rem] leading-none {{ request('sort_by') === 'name' && request('sort_dir','desc') === 'desc' ? 'text-indigo-600 font-bold' : '' }}">
                                    ▼
                                </span>
                            </span>
                        </span>

                    </button>
                </th>
                
                {{-- File No. --}}
                <th class="px-6 py-4 text-left font-semibold text-gray-700">File No.</th>
                
                {{-- Status --}}
                <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
                
                {{-- Total --}}
                <th class="px-6 py-4 text-right font-semibold text-gray-700">Total</th>
                
                {{-- Outstanding --}}
                <th class="px-6 py-4 text-right font-semibold text-gray-700">Outstanding</th>
                
                {{-- Created - CLICKABLE --}}
                <th class="px-6 py-4 text-left">
                    <button type="button"
                            class="sort-btn group flex items-center justify-between w-full
                                font-semibold text-gray-700 hover:text-indigo-700
                                bg-gray-50 hover:bg-indigo-50
                                border border-transparent hover:border-indigo-300
                                rounded-xl px-3 py-2 shadow-sm hover:shadow-md
                                transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            data-sort="date">

                        <span class="flex items-center gap-2">
                            Created
                            {{-- Vertical arrows --}}
                            <span class="flex flex-col justify-center text-gray-400 group-hover:text-indigo-500">
                                {{-- Up arrow --}}
                                <span class="text-[0.55rem] leading-none {{ request('sort_by') === 'date' && request('sort_dir','desc') === 'asc' ? 'text-indigo-600 font-bold' : '' }}">
                                    ▲
                                </span>
                                {{-- Down arrow --}}
                                <span class="text-[0.55rem] leading-none {{ request('sort_by') === 'date' && request('sort_dir','desc') === 'desc' ? 'text-indigo-600 font-bold' : '' }}">
                                    ▼
                                </span>
                            </span>
                        </span>

                    </button>
                </th>

                    </button>
                </th>
                
                {{-- Actions --}}
                <th class="px-6 py-4 text-right font-semibold text-gray-700">Actions</th>
            </tr>
        </thead>
            
            <tbody class="divide-y divide-gray-50">
                @forelse(($quotations['items'] ?? []) as $quotation)
                    <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-blue-50 hover:shadow-sm transition-all duration-200 group">
                        {{-- ID --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl flex items-center justify-center shadow-inner ring-1 ring-indigo-100/50">
                                <span class="text-sm font-bold text-indigo-800">{{ $quotation['id'] }}</span>
                            </div>
                        </td>
                        
                        {{-- Patient --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg ring-2 ring-white/50">
                                    <span class="text-white text-sm font-bold">
                                        {{ substr($quotation['patient']['full_name'] ?? 'N/A', 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 leading-tight">
                                        {{ $quotation['patient']['full_name'] ?? 'No Patient' }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">Patient Record</div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- File No. --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($quotation['patient']['file_number'] ?? null)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-gray-100 to-gray-50 text-gray-800 shadow-sm border border-gray-200">
                                    {{ $quotation['patient']['file_number'] }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm font-medium">No File</span>
                            @endif
                        </td>
                        
                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $quotation['status'] ?? 'unknown';
                                $statusConfig = match($status) {
                                    'draft'     => ['bg-gradient-to-r from-amber-100 to-yellow-100', 'text-amber-800', 'ring-amber-200'],
                                    'confirmed' => ['bg-gradient-to-r from-blue-100 to-indigo-100', 'text-blue-800', 'ring-blue-200'],
                                    'converted' => ['bg-gradient-to-r from-emerald-100 to-green-100', 'text-emerald-800', 'ring-emerald-200'],
                                    default     => ['bg-gradient-to-r from-gray-100 to-gray-200', 'text-gray-800', 'ring-gray-200'],
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm {{ $statusConfig[0] }} {{ $statusConfig[1] }} ring-1 {{ $statusConfig[2] }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        
                        {{-- Total --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-lg font-bold bg-gradient-to-r from-gray-900 to-gray-800 bg-clip-text text-transparent">
                                {{ number_format($quotation['total'] ?? 0, 2) }} {{ $defaultCurrency }}
                            </div>
                            <div class="text-xs text-gray-500 font-medium">Net Amount</div>
                        </td>

                        {{-- Outstanding --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @php
                                // Normalize value safely
                                $outstandingRaw = $quotation['outstanding'] ?? 0;
                                $outstanding = is_numeric($outstandingRaw) ? floatval($outstandingRaw) : 0;
                                $status = $quotation['status'] ?? '';
                                $isPaid = $status !== 'draft' && $outstanding < 0.01; // anything < 0.01 counts as paid
                            @endphp

                            <div class="flex items-center justify-end gap-2">
                                @if($isPaid)
                                    {{-- Paid --}}
                                    <div class="w-3 h-3 bg-emerald-400 rounded-full shadow-sm"></div>
                                    <span class="text-sm font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full shadow-sm">
                                        Paid
                                    </span>
                                @else
                                    {{-- Outstanding --}}
                                    <div class="w-3 h-3 bg-red-400 rounded-full shadow-sm"></div>
                                    <span class="text-sm font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full shadow-sm">
                                        {{ number_format($outstanding, 2) }} {{ $defaultCurrency }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Created --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($quotation['created_at'])->format('M d, Y') }}
                            </div>
                            <div class="text-xs font-mono text-gray-500 bg-gray-50 px-2 py-0.5 rounded">
                                {{ \Carbon\Carbon::parse($quotation['created_at'])->format('H:i') }}
                            </div>
                        </td>
                        
                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                {{-- View --}}
                                <a href="{{ route('quotations.show', $quotation['id']) }}"
                                   title="View Details"
                                   class="p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 hover:shadow-sm hover:scale-105 group-hover:text-blue-700 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                {{-- Edit --}}
                                <a href="{{ route('quotations.edit', $quotation['id']) }}"
                                   title="Edit"
                                   class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:shadow-sm hover:scale-105 group-hover:text-indigo-700 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                {{-- Delete --}}
                                <form action="{{ route('quotations.destroy', $quotation['id']) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            title="Delete"
                                            class="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:shadow-sm hover:scale-105 group-hover:text-red-700 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900">No quotations found</h3>
                                <p class="text-gray-500 max-w-md">No quotations match your current filters. Create your first quotation to get started.</p>
                                <a href="{{ route('quotations.create') }}"
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create First Quotation
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(($quotations['last_page'] ?? 1) > 1)
        <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50/50 to-transparent">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Page {{ $quotations['page'] }} of {{ $quotations['last_page'] }} —
                    Showing {{ count($quotations['items'] ?? []) }} of {{ $quotations['total'] }} results
                </div>
                <div class="flex items-center gap-1">
                    @for ($i = max(1, ($quotations['page'] ?? 1) - 2); $i <= min(($quotations['last_page'] ?? 1), ($quotations['page'] ?? 1) + 2); $i++)
                        <a href="?page={{ $i }}"
                           data-page="{{ $i }}"
                           class="px-3 py-2 rounded-xl font-semibold transition-all {{ ($i == ($quotations['page'] ?? 1))
                                ? 'bg-indigo-600 text-white shadow-lg hover:shadow-xl hover:scale-105'
                                : 'bg-white/50 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 border border-gray-200 hover:shadow-sm' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            </div>
        </div>
    @endif
</div>


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log('🔧 Quotations table initializing...');

    const form = document.getElementById("quotations-search-form");
    const container = document.getElementById("quotations-table-container");
    const route = container?.getAttribute("data-table-route");

    console.log('📋 Form:', form);
    console.log('📦 Container:', container);
    console.log('🛤️ Route:', route);

    if (!form || !container || !route) {
        console.error('❌ Missing required elements');
        return;
    }

    // Build query string from form + extra params
    function buildParams(extra = {}) {
        const fd = new FormData(form);

        const sortBySelect = form.querySelector("[name='sort_by']");
        const sortDirSelect = form.querySelector("[name='sort_dir']");
        
        const sortBy = sortBySelect ? sortBySelect.value : "";
        const sortDir = sortDirSelect ? sortDirSelect.value : "desc";

        fd.set('sort_by', sortBy);
        fd.set('sort_dir', sortDir);

        const params = new URLSearchParams(fd);

        // override/add extra params like page
        Object.keys(extra).forEach(key => {
            if (extra[key] !== null && extra[key] !== undefined && extra[key] !== '') {
                params.set(key, extra[key]);
            }
        });

        const queryString = params.toString();
        console.log('📊 Params built:', queryString);
        return queryString;
    }

    // Pagination links
    function bindPagination() {
        const paginationLinks = container.querySelectorAll(".pagination a[data-page]");
        console.log(`📄 Found ${paginationLinks.length} pagination links`);
        paginationLinks.forEach((link, i) => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                const page = this.getAttribute("data-page");
                console.log(`📄 Page ${page} clicked`);
                fetchQuotations(buildParams({ page: page }));
            });
        });
    }

    // SORTING BUTTONS - FIXED
    function bindSortButtons() {
        const buttons = container.querySelectorAll("button[data-sort]");
        console.log(`🔘 Found ${buttons.length} sort buttons`);

        buttons.forEach(btn => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                const sortBy = this.dataset.sort;
                console.log(`🔘 Sort button clicked: ${sortBy}`);

                const sortBySelect = form.querySelector("[name='sort_by']");
                const sortDirSelect = form.querySelector("[name='sort_dir']");

                if (!sortBySelect || !sortDirSelect) {
                    console.error('❌ Sort selects not found');
                    return;
                }

                const currentBy = sortBySelect.value;
                const currentDir = sortDirSelect.value || "desc";
                
                // Toggle direction ONLY if same column clicked twice
                let nextDir = "desc";
                if (currentBy === sortBy) {
                    nextDir = currentDir === "asc" ? "desc" : "asc";
                }

                console.log(`🔄 Setting sort_by=${sortBy}, sort_dir=${nextDir}`);

                sortBySelect.value = sortBy;
                sortDirSelect.value = nextDir;

                fetchQuotations(buildParams({ page: 1 }));
            });
        });
    }

    // Fetch quotations via AJAX
    function fetchQuotations(queryString = null) {
        const qs = queryString ?? buildParams();
        console.log(`🌐 Fetching: ${route}?${qs}`);

        fetch(route + "?" + qs, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => {
            console.log(`📥 Response: ${response.status} ${response.statusText}`);
            return response.text();
        })
        .then(html => {
            console.log('✅ HTML loaded, rebinding...');
            container.innerHTML = html;
            bindPagination();
            bindSortButtons();
        })
        .catch(err => console.error("❌ AJAX error:", err));
    }

    // Live search
    const searchInput = form.querySelector("[name='q']");
    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            console.log('🔍 Search typing...');
            fetchQuotations();
        });
    }

    // Filter by status
    const statusSelect = form.querySelector("[name='status']");
    if (statusSelect) {
        statusSelect.addEventListener("change", () => {
            console.log('📋 Status changed:', statusSelect.value);
            fetchQuotations();
        });
    }

    // Initial binding
    console.log('🚀 Initial binding...');
    bindPagination();
    bindSortButtons();
});
</script>
@endpush
