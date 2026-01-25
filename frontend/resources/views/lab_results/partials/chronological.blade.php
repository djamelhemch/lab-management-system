{{-- Search and Actions Bar --}}
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="relative flex-1 max-w-md">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" 
               id="searchResults"
               placeholder="Rechercher un résultat, patient, analyse..." 
               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition">
    </div>
    
   
</div>
{{-- Filters --}}
<div class="mb-6 flex flex-wrap items-center gap-4">
    <form method="GET" action="{{ route('lab-results.index') }}" class="flex flex-wrap gap-2 items-center">
        <input type="date" name="from_date" value="{{ request('from_date') }}"
               class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">

        <input type="date" name="to_date" value="{{ request('to_date') }}"
               class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">

        <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
            Filtrer
        </button>

        <a href="{{ route('lab-results.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
           Réinitialiser
        </a>
         <button onclick="window.location.reload()" 
            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Actualiser
    </button>
    
    </form>
</div>
{{-- Sorting controls --}}
<div class="mb-4 flex flex-wrap items-center gap-4">
    <span class="font-medium text-gray-700">Trier par :</span>

    <a href="{{ route('lab-results.index', array_merge(request()->all(), ['sort' => 'date_desc'])) }}"
       class="px-3 py-1 rounded-md text-sm font-medium {{ request('sort') === 'date_desc' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
        Date ↓
    </a>

    <a href="{{ route('lab-results.index', array_merge(request()->all(), ['sort' => 'date_asc'])) }}"
       class="px-3 py-1 rounded-md text-sm font-medium {{ request('sort') === 'date_asc' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
        Date ↑
    </a>

    <a href="{{ route('lab-results.index', array_merge(request()->all(), ['sort' => 'file_number_asc'])) }}"
       class="px-3 py-1 rounded-md text-sm font-medium {{ request('sort') === 'file_number_asc' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
        N° Dossier ↑
    </a>

    <a href="{{ route('lab-results.index', array_merge(request()->all(), ['sort' => 'file_number_desc'])) }}"
       class="px-3 py-1 rounded-md text-sm font-medium {{ request('sort') === 'file_number_desc' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
        N° Dossier ↓
    </a>
    
</div>
{{-- Results Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">N° Dossier</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Analyse</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Résultat</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Plage Normale</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Appareil</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody" class="divide-y divide-gray-200">
                @forelse ($labResults as $result)
                    @php
                        $isCritical = strtolower($result['interpretation'] ?? '') === 'critical';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <span class="text-red-600 font-semibold text-sm">
                                        {{ strtoupper(substr($result['patient_first_name'] ?? 'U', 0, 1) . substr($result['patient_last_name'] ?? 'N', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $result['patient_first_name'] ?? '' }} {{ $result['patient_last_name'] ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono text-gray-700">{{ $result['file_number'] ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $result['analysis_code'] ?? '—' }}</div>
                            <div class="text-sm text-gray-500">{{ $result['analysis_name'] ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold {{ $isCritical ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $result['result_value'] ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($result['normal_min'] !== null && $result['normal_max'] !== null)
                                {{ $result['normal_min'] }} - {{ $result['normal_max'] }}
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $result['device_name'] ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isCritical)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    Critique
                                </span>
                            @elseif($result['status'] === 'final')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    Final
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                                    </svg>
                                    En Cours
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($result['created_at'])->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('lab-results.show', $result['id']) }}" 
                               class="text-red-600 hover:text-red-900 font-medium">
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun résultat disponible</h3>
                            <p class="mt-1 text-sm text-gray-500">Les résultats d'analyses apparaîtront ici.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchResults')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#resultsTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endpush
