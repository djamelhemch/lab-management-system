<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    {{-- Component Header --}}
    <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Quotations Overview</h3>
            </div>
            <div class="text-sm text-gray-500">
                @php
                    $quotationsCount = is_array($quotations) ? count($quotations) : $quotations->count();
                @endphp
                {{ $quotationsCount }} {{ Str::plural('quotation', $quotationsCount) }} found
            </div>
        </div>
    </div>

    {{-- Responsive Table Wrapper --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">File No.</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Outstanding</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse(($quotations['items'] ?? []) as $quotation)
                    <tr class="hover:bg-indigo-50 transition-colors duration-200 group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors duration-200">
                                <span class="text-sm font-semibold text-indigo-700">{{ $quotation['id'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">
                                        {{ substr($quotation['patient']['full_name'] ?? 'N/A', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $quotation['patient']['full_name'] ?? 'No Patient' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Patient Record</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($quotation['patient']['file_number'] ?? null)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $quotation['patient']['file_number'] }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">No File</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $quotation['status'] ?? 'unknown';
                                $classes = match($status) {
                                    'draft'     => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                    'confirmed' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'converted' => 'bg-green-100 text-green-800 border border-green-200',
                                    default     => 'bg-gray-100 text-gray-800 border border-gray-200',
                                };
                            @endphp

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $classes }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-gray-900">
                                {{ number_format($quotation['total'] ?? 0, 2) }} {{ $defaultCurrency }}
                            </div>
                            <div class="text-xs text-gray-500">Net Amount</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          @php
                            $outstanding = floatval($quotation['outstanding'] ?? 0);
                            $status = $quotation['status'] ?? '';
                        @endphp
                        <div class="flex items-center gap-2">
                            @if($status !== 'draft' && $outstanding <= 0)
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                <span class="text-sm font-semibold text-green-600">Paid</span>
                            @else
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                <span class="text-sm font-semibold text-red-600">
                                    {{ number_format($outstanding, 2) }} {{ $defaultCurrency }}
                                </span>
                            @endif
                        </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($quotation['created_at'])->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($quotation['created_at'])->format('H:i') }}</span>
                            </div>
                        </td>
                       <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">

                            {{-- View Button --}}
                            <a href="{{ route('quotations.show', $quotation['id']) }}" title="View Details"
                            class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>

                            {{-- Edit Button --}}
                            <a href="{{ route('quotations.edit', $quotation['id']) }}" title="Edit Quotation"
                            class="inline-flex items-center justify-center p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            {{-- Delete Button with Correct onsubmit --}}
                            <form action="{{ route('quotations.destroy', $quotation['id']) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this quotation? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete Quotation"
                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No quotations found</h3>
                                <p class="text-gray-500 mb-6">Create your first quotation to get started.</p>
                                <a href="{{ route('quotations.create') }}" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl">Create Quotation</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(($quotations['last_page'] ?? 1) > 1)
        <div class="px-6 py-3 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Page {{ $quotations['page'] }} of {{ $quotations['last_page'] }} — 
                Total: {{ $quotations['total'] }}
            </div>
            <div class="flex space-x-2">
                @for ($i = 1; $i <= ($quotations['last_page'] ?? 1); $i++)
                    <a href="?page={{ $i }}"
                    class="px-3 py-1 rounded {{ $i == ($quotations['page'] ?? 1) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $i }}
                    </a>
                @endfor
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("quotations-search-form");
    const container = document.getElementById("quotations-table-container");
    const route = container.getAttribute("data-table-route");

    function fetchQuotations(params = null) {
        const queryParams = params 
            ? params 
            : new URLSearchParams(new FormData(form)).toString();

        fetch(route + "?" + queryParams, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            // re-bind pagination links after reload
            container.querySelectorAll(".pagination a").forEach(link => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    fetchQuotations(url.searchParams.toString());
                });
            });
        })
        .catch(err => console.error("Error fetching quotations:", err));
    }

    // live search
    const searchInput = form.querySelector("[name='q']");
    if (searchInput) {
        searchInput.addEventListener("keyup", () => fetchQuotations());
    }

    // filter by status
    const statusSelect = form.querySelector("[name='status']");
    if (statusSelect) {
        statusSelect.addEventListener("change", () => fetchQuotations());
    }

    // initial pagination binding
    container.querySelectorAll(".pagination a").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            fetchQuotations(url.searchParams.toString());
        });
    });
});
</script>
@endpush