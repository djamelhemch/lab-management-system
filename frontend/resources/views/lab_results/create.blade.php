@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Créer des résultats</h1>
            <p class="text-gray-600 mt-1">Sélectionner un devis puis saisir les résultats pour chaque analyse.</p>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex items-start animate-fade-in">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-start animate-fade-in">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg animate-fade-in">
                <ul class="text-sm list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Step 1: Select quotation --}}
        <div class="mb-8 p-6 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl hover:border-red-300 transition-all">
            <label for="quotation_select" class="block text-sm font-semibold text-gray-700 mb-3">
                📋 Sélectionner un devis
            </label>
            <select id="quotation_select" class="w-full p-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 focus:outline-none text-sm bg-white transition-all hover:shadow-md">
                <option value="">🔍 Choisir un devis (patient & date)</option>
                @foreach($quotations as $q)
                    @php
                        $patient = $q['patient'] ?? null;
                        $patientName = $patient ? trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')) : 'Patient inconnu';
                        $visitDate = $q['created_at'] ?? $q['visit_date'] ?? null;
                    @endphp
                    <option value="{{ $q['id'] }}">
                        #{{ $q['id'] }} — {{ $patientName }} @if($visitDate)({{ \Illuminate\Support\Str::limit($visitDate, 10, '') }})@endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Step 2: Results table --}}
        <div id="resultsSection" style="display:none;">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <div id="loadingSpinner" class="hidden animate-spin rounded-full h-6 w-6 border-b-2 border-red-600 mr-3"></div>
                    <h3 id="sectionTitle" class="text-xl font-bold text-gray-900"></h3>
                </div>
                <div id="itemCount" class="text-sm text-gray-500 font-medium"></div>
            </div>

            <form id="resultsForm" method="POST" action="{{ route('lab-results.store-bulk') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="quotation_id" id="quotation_id">

                <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-red-50 to-red-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Code Analyse</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">Plage normale</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Valeur mesurée</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">Interprétation</th>
                            </tr>
                        </thead>
                        <tbody id="analysisItemsBody" class="divide-y divide-gray-100">
                            {{-- Dynamic rows --}}
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span id="filledCount">0</span> / <span id="totalCount">-</span> résultats saisis
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="clearForm()" 
                                class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-gray-500">
                            🗑️ Effacer
                        </button>
                        <button type="submit" id="submitBtn" disabled
                                class="inline-flex items-center px-8 py-2 border border-transparent text-sm font-semibold rounded-lg shadow-lg text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                            💾 Enregistrer <span id="submitCount" class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs font-bold">0</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Feedback area --}}
        <div id="feedback" class="mt-8 p-4 rounded-xl hidden shadow-lg border-2 mx-auto max-w-2xl"></div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
.result-input:focus {
    box-shadow: 0 0 0 3px rgb(239 68 68 / 0.1);
}
.interpretation {
    transition: all 0.2s ease-in-out;
}
</style>

<script>

const API_BASE = "{{ env('FASTAPI_URL') }}";
const API_TOKEN = "{{ $fastapi_token ?? '' }}";

const els = {
    quotationSelect: document.getElementById('quotation_select'),
    analysisBody: document.getElementById('analysisItemsBody'),
    resultsSection: document.getElementById('resultsSection'),
    resultsForm: document.getElementById('resultsForm'),
    quotationIdInput: document.getElementById('quotation_id'), // <- hidden input
    sectionTitle: document.getElementById('sectionTitle'),
    loadingSpinner: document.getElementById('loadingSpinner'),
    submitBtn: document.getElementById('submitBtn'),
    submitCount: document.getElementById('submitCount'),
    feedback: document.getElementById('feedback'),
    filledCount: document.getElementById('filledCount'),
    totalCount: document.getElementById('totalCount'),
    itemCount: document.getElementById('itemCount')
};

els.quotationSelect.addEventListener('change', () => {
    els.quotationIdInput.value = els.quotationSelect.value || '';
    loadQuotationItems();
});

let resultInputs = [];
let totalItems = 0;
let currentItems = [];



async function loadQuotationItems() {
    const quotationId = els.quotationSelect.value;
    
    // Reset
    els.analysisBody.innerHTML = '';
    els.resultsForm.style.display = 'none';
    els.resultsSection.style.display = 'none';
    els.feedback.classList.add('hidden');
    els.submitBtn.disabled = true;
    els.submitCount.textContent = '0';
    resultInputs = [];
    currentItems = []; 

    if (!quotationId) return;

    showLoading(true);
    els.sectionTitle.textContent = 'Chargement des analyses...';
    
    try {
        const resp = await fetch(`${API_BASE}/quotations/${quotationId}`, {
            headers: {
                'Authorization': `Bearer ${API_TOKEN}`,
                'Accept': 'application/json',
            },
        });

        if (!resp.ok) {
            const errText = await resp.text();
            throw new Error(`HTTP ${resp.status}: ${resp.statusText} - ${errText.slice(0, 100)}`);
        }

        const quotation = await resp.json();
        const items = quotation.analysis_items || [];
        currentItems = items; 
        
        if (!items.length) {
            showFeedback('❌ Aucune analyse trouvée dans ce devis.', 'warning');
            return;
        }

        // Build table rows
        items.forEach(item => {
            const analysis = item.analysis || {};
            const normalRanges = analysis.normal_ranges ?? [];
            console.log(
                'Analysis ID:',
                analysis.id,
                'normal_ranges:',
                normalRanges
            );

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 hover:shadow-sm transition-all group';
            row.innerHTML = `
                <td class="px-6 py-4 font-semibold text-gray-900 group-hover:text-red-700">
                    <div class="text-sm font-mono">${analysis.code || `Analyse #${item.analysis_id}`}</div>
                    <div class="text-xs text-gray-600">${analysis.name || 'Nom non défini'}</div>
                </td>

                <td class="px-6 py-4 space-y-1">
                    ${formatNormalRanges(analysis.normal_ranges || [], analysis.unit?.name)}
                </td>

                <td class="px-6 py-4">
                    <input
                        type="text"
                        name="result_values[${item.id}]"
                        data-item-id="${item.id}"
                        class="result-input w-32 px-3 py-2 rounded-lg border-2 border-gray-200 shadow-sm focus:border-red-400 focus:ring-2 focus:ring-red-200 focus:outline-none transition-all hover:border-gray-300 text-sm"
                        placeholder="Ex: 4.2"
                    >
                </td>

                <td class="px-6 py-4">
                    <span class="interpretation inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700" data-item-id="${item.id}">
                        —
                    </span>
                </td>
            `;
                        
            els.analysisBody.appendChild(row);
            
            // Event listeners
            const input = row.querySelector('.result-input');
            resultInputs.push(input);
            input.addEventListener('input', handleInputChange);
        });

        // Show results
        totalItems = items.length;
        els.totalCount.textContent = totalItems;
        els.sectionTitle.innerHTML = `📊 ${totalItems} analyse(s) - <span class="text-red-600">${quotation.patient?.first_name || ''} ${quotation.patient?.last_name || ''}</span>`;
        els.resultsSection.style.display = 'block';
        els.resultsForm.style.display = 'block';
        
        els.itemCount.textContent = `${totalItems} analyses • Plages normales visibles`;
        showFeedback(`✅ ${totalItems} analyses chargées avec succès!`, 'success');

    } catch (error) {
        console.error('Load error:', error);
        showFeedback(`❌ Erreur de chargement: ${error.message}`, 'error');
    } finally {
        showLoading(false);
    }
}

function formatNormalRanges(normalRanges, unit) {
    if (!Array.isArray(normalRanges) || normalRanges.length === 0) {
        return '<span class="text-gray-400 italic text-xs">—</span>';
    }

    return normalRanges.map(r => {
        if (!r) return '';

        const sex =
            r.sex_applicable === 'M' ? '♂' :
            r.sex_applicable === 'F' ? '♀' :
            'All';

        const min = r.normal_min ?? '?';
        const max = r.normal_max ?? '?';
        const unitText = unit?.name ?? '';

        return `
            <span class="inline-block mb-1 px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-mono">
                ${sex}: ${min} – ${max} ${unitText}
            </span>
        `;
    }).join('');
}

function handleInputChange(e) {
    const input = e.target;
    const row = input.closest('tr');
    const interpSpan = row.querySelector('.interpretation');
    const value = input.value.trim();

    // Find the analysis object for this row
    const itemId = parseInt(input.dataset.itemId);
    const item = currentItems.find(i => i.id === itemId);
    const normalRanges = (item?.analysis?.normal_ranges || []);

    if (!value) {
        interpSpan.innerHTML = '—';
        interpSpan.className = 'interpretation inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700';
    } else {
        const num = parseFloat(value);

        if (isNaN(num)) {
            interpSpan.innerHTML = '⚠️ Invalide';
            interpSpan.className = 'interpretation inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800';
        } else if (normalRanges.length === 0) {
            interpSpan.innerHTML = '—';
            interpSpan.className = 'interpretation inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700';
        } else {
            // Check against all ranges (pick first matching sex if needed)
            let matched = false;
            for (const r of normalRanges) {
                const min = r.normal_min;
                const max = r.normal_max;
                if (min != null && max != null && num >= min && num <= max) {
                    matched = true;
                    break;
                }
            }

            if (matched) {
                interpSpan.innerHTML = '✅ Normal';
                interpSpan.className = 'interpretation inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 animate-pulse';
            } else {
                interpSpan.innerHTML = '⚠️ Hors plage';
                interpSpan.className = 'interpretation inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800';
            }
        }
    }

    updateCounts();
}

function updateCounts() {
    const filled = resultInputs.filter(input => input.value.trim()).length;
    els.filledCount.textContent = filled;
    els.submitCount.textContent = filled;
    els.submitBtn.disabled = filled === 0;
}

function showLoading(show) {
    els.loadingSpinner.classList.toggle('hidden', !show);
}

function showFeedback(message, type = 'info') {
    els.feedback.innerHTML = message;
    els.feedback.className = `mt-8 p-6 rounded-2xl shadow-xl border-4 mx-auto max-w-2xl animate-fade-in ${
        type === 'success' ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-300 text-green-800' :
        type === 'error' ? 'bg-gradient-to-r from-red-50 to-rose-50 border-red-300 text-red-800' :
        type === 'warning' ? 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-300 text-yellow-800' :
                           'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-300 text-blue-800'
    }`;
    els.feedback.classList.remove('hidden');
    
    if (type === 'success') {
        setTimeout(() => els.feedback.classList.add('hidden'), 4000);
    }
}

function clearForm() {
    if (confirm('🗑️ Effacer tous les résultats saisis?\n\nCette action est irréversible.')) {
        resultInputs.forEach(input => {
            input.value = '';
            input.dispatchEvent(new Event('input'));
        });
        showFeedback('✅ Formulaire effacé', 'success');
    }
}

// Form events
els.resultsForm.addEventListener('submit', function() {
    els.submitBtn.disabled = true;
    els.submitBtn.innerHTML = '⏳ Enregistrement...';
    showFeedback('💾 Sauvegarde des résultats en cours...', 'info');
});
</script>
@endsection
