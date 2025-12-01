{{-- Search Bar --}}
<div class="mb-6 flex justify-between items-center">
    <div class="relative flex-1 max-w-md">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" 
               id="searchPatients"
               placeholder="Rechercher un patient..." 
               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition">
    </div>
    <div class="text-sm text-gray-600 font-medium">
        {{ $patients->count() }} patient(s)
    </div>
</div>

{{-- Patient Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="patientGrid">
    @forelse($patients as $patient)
        <a href="{{ route('lab-results.patient', $patient['id']) }}" 
           class="patient-card group block bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-red-300 transition-all duration-200">
            <div class="p-5">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-md">
                        <span class="text-white font-bold text-lg">
                            {{ strtoupper(substr($patient['first_name'] ?? 'U', 0, 1) . substr($patient['last_name'] ?? 'N', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-semibold text-gray-900 truncate group-hover:text-red-600 transition-colors">
                            {{ $patient['first_name'] ?? '' }} {{ $patient['last_name'] ?? '' }}
                        </p>
                        <p class="text-sm text-gray-500 font-mono mt-0.5">
                            N° {{ $patient['file_number'] ?? 'N/A' }}
                        </p>
                        @if($patient['dob'] ?? null)
                            <p class="text-xs text-gray-400 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($patient['dob'])->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full">
            <div class="text-center py-16 bg-white rounded-lg border-2 border-dashed border-gray-300">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun patient trouvé</h3>
                <p class="mt-2 text-sm text-gray-500">Aucun patient n'est enregistré dans la base de données.</p>
            </div>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
document.getElementById('searchPatients')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.patient-card');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endpush
