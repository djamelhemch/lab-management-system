{{-- resources/views/analyses/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add New Analysis')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
  <div class="max-w-5xl mx-auto">

    {{-- Page Header --}}
    <header class="mb-10">
      <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
        <x-heroicon-o-beaker class="w-7 h-7 text-[#bc1622]" />
        Add New Analysis
      </h1>
      <p class="mt-1 text-sm text-gray-600">Define a new laboratory analysis including its category, unit, pricing, and device compatibility.</p>
    </header>

    <form action="{{ route('analyses.store') }}" method="POST" class="space-y-8" novalidate>
      @csrf

      {{-- BASIC INFORMATION --}}
      <section class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-2">
          <x-heroicon-o-clipboard-document class="w-5 h-5 text-[#bc1622]" />
          <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
        </div>

        <div class="p-6 space-y-6">
          <div class="grid md:grid-cols-2 gap-6">
            {{-- Code --}}
            <div>
              <label for="code" class="block text-sm font-medium text-gray-900">Analysis Code</label>
              <p class="text-sm text-gray-500 mb-2">Internal reference code (optional)</p>
              <input id="code" name="code" value="{{ old('code') }}" type="text" placeholder="e.g., CBC001"
                class="w-full rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]" />
              @error('code')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Name --}}
            <div>
              <label for="name" class="block text-sm font-medium text-gray-900">Analysis Name</label>
              <p class="text-sm text-gray-500 mb-2">Short, descriptive name shown to clinicians</p>
              <input id="name" name="name" value="{{ old('name') }}" type="text" required placeholder="e.g., Complete Blood Count"
                class="w-full rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]" />
              @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <hr class="border-gray-200">

          <div class="grid md:grid-cols-3 gap-6">
            {{-- Category --}}
            <div>
              <label class="block text-sm font-medium text-gray-900">Category</label>
              <div class="mt-2 flex gap-2">
                <select name="category_analyse_id" class="flex-1 rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]">
                  <option value="">Select category</option>
                  @foreach($categories as $category)
                    <option value="{{ $category['id'] }}" {{ old('category_analyse_id') == $category['id'] ? 'selected' : '' }}>
                      {{ $category['name'] }}
                    </option>
                  @endforeach
                </select>
                <button type="button" onclick="openCategoryModal()"
                  class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700">
                  <x-heroicon-o-plus-small class="w-5 h-5" />
                </button>
              </div>
            </div>

            {{-- Price --}}
            <div>
              <label for="price" class="block text-sm font-medium text-gray-900">Price (DA)</label>
              <div class="mt-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">DA</span>
                <input id="price" name="price" type="number" step="0.01" min="0"
                  value="{{ old('price') }}" class="pl-10 w-full rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]"
                  placeholder="0.00" />
              </div>
            </div>

            {{-- Unit --}}
            <div>
              <label class="block text-sm font-medium text-gray-900">Unit</label>
              <div class="mt-2 flex gap-2">
                <select name="unit_id" class="flex-1 rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]">
                  <option value="">Select unit</option>
                  @foreach($units as $unit)
                    <option value="{{ $unit['id'] }}" {{ old('unit_id') == $unit['id'] ? 'selected' : '' }}>
                      {{ $unit['name'] }}
                    </option>
                  @endforeach
                </select>
                <button type="button" onclick="openUnitModal()"
                  class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700">
                  <x-heroicon-o-plus-small class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      {{-- SPECIFICATIONS --}}
      <section class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-2">
          <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-[#bc1622]" />
          <h2 class="text-lg font-semibold text-gray-900">Specifications</h2>
        </div>

        <div class="p-6 space-y-8">
          {{-- Reference Ranges --}}
          <div>
            <div class="flex justify-between items-center mb-3">
              <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-1">
                <x-heroicon-o-chart-bar class="w-4 h-4 text-[#bc1622]" /> Normal Reference Ranges
              </h3>
              <button type="button" onclick="addNormalRange()"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700">
                <x-heroicon-o-plus class="w-4 h-4" /> Add Range
              </button>
            </div>
            <div id="normalRangesContainer" class="divide-y divide-gray-200 border border-gray-200 rounded-md"></div>
            <div id="noRangesMessage" class="text-center py-8 text-gray-500 text-sm border border-dashed border-gray-200 rounded-md mt-3">
              No ranges defined yet. Click “Add Range” to begin.
            </div>
          </div>

          <hr class="border-gray-200">

          {{-- Sample Requirements --}}
          <div class="grid md:grid-cols-2 gap-6">
            {{-- Sample Type --}}
            <div>
              <label class="block text-sm font-medium text-gray-900">Sample Type</label>
              <div class="mt-2 flex gap-2">
                <select name="sample_type_id" class="flex-1 rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]">
                  <option value="">Select type</option>
                  @foreach($sampleTypes as $sampleType)
                    <option value="{{ $sampleType['id'] }}" {{ old('sample_type_id') == $sampleType['id'] ? 'selected' : '' }}>
                      {{ $sampleType['name'] }}
                    </option>
                  @endforeach
                </select>
                <button type="button" onclick="openSampleTypeModal()"
                  class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700">
                  <x-heroicon-o-plus-small class="w-5 h-5" />
                </button>
              </div>
            </div>

            {{-- Tube Type --}}
            <div>
              <label for="tube_type" class="block text-sm font-medium text-gray-900">Tube Type</label>
              <select id="tube_type" name="tube_type"
                class="mt-2 w-full rounded-lg border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622] bg-white">
                <option value="">Select tube type</option>
                <option value="EDTA">EDTA (Lavender)</option>
                <option value="Heparin">Heparin (Green)</option>
                <option value="Citrated">Citrated (Light Blue)</option>
                <option value="Dry">Serum (Red/Gold)</option>
              </select>
            </div>
          </div>

          <hr class="border-gray-200">

          {{-- Devices --}}
          <div>
            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-1">
              <x-heroicon-o-cpu-chip class="w-4 h-4 text-[#bc1622]" /> Compatible Devices
            </h3>
            <div id="deviceSelectionContainer" class="border border-gray-200 divide-y divide-gray-200 rounded-md">
              <div class="h-12 bg-gray-100 animate-pulse"></div>
              <div class="h-12 bg-gray-100 animate-pulse"></div>
              <div class="h-12 bg-gray-100 animate-pulse"></div>
            </div>
            <div id="deviceSelectionSummary" class="hidden mt-4 pt-4 border-t border-gray-200">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Selected devices</span>
                <span id="selectedDeviceCount" class="font-medium text-gray-900">0</span>
              </div>
            </div>
          </div>
        </div>
      </section>
<div class="border-2 border-gray-300 rounded-2xl p-6 bg-white shadow-md mt-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
      🧮 Formule de calcul
    </h3>
    <button 
      type="button" 
      onclick="openSavedFormulas()"
      class="text-sm text-indigo-600 hover:underline"
    >
      📚 Formules enregistrées
    </button>
  </div>

  <!-- Formula Name -->
  <div class="mb-4">
    <label for="formula_name" class="block text-sm font-medium text-gray-700 mb-1">
      Nom de la formule :
    </label>
    <input 
      type="text" 
      id="formula_name" 
      class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" 
      placeholder="Ex : Formule de Friedewald"
    >
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- LEFT: Editor -->
    <div>
      <label for="formula" class="block text-sm font-medium text-gray-700 mb-2">
        Expression mathématique :
      </label>
      <div class="border-2 border-blue-400 rounded-xl p-3 bg-blue-50 focus-within:ring-2 focus-within:ring-blue-300 transition">
        <textarea 
          id="formula" 
          rows="4" 
          class="w-full bg-transparent outline-none text-gray-800 text-sm font-mono"
          placeholder="Exemple : LDL-C = CT - HDL-C - TG / 5"
        ></textarea>
      </div>

      <div id="formulaPreview" class="mt-3 text-sm font-mono text-gray-600 bg-gray-100 border border-gray-300 rounded-lg p-2">
        🧠 Aperçu : <span id="formulaDisplay" class="text-gray-800"></span>
      </div>

      <div class="flex gap-2 mt-3">
        <button 
          type="button" 
          onclick="clearFormula()"
          class="bg-red-50 border border-red-300 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition"
        >
          Effacer
        </button>
        <button 
          id="saveFormulaBtn"
          type="button"
          onclick="saveFormula()"
          class="bg-green-50 border border-green-300 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-100 transition"
        >
          💾 Enregistrer la formule
        </button>
      </div>
    </div>

    <!-- RIGHT: Tools -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">🔢 Opérateurs et fonctions :</label>
      <div class="border-2 border-gray-300 bg-gray-50 rounded-xl p-3 flex flex-wrap gap-2">
        @php
          $ops = [
            ['+', 'Addition'],
            ['-', 'Soustraction'],
            ['*', 'Multiplication'],
            ['/', 'Division'],
            ['=', 'Égal (assignation du résultat)'],
            ['(', 'Parenthèse ouvrante'],
            [')', 'Parenthèse fermante'],
            ['^', 'Puissance'],
            ['√', 'Racine carrée'],
            ['ln()', 'Logarithme naturel'],
            ['exp()', 'Exponentielle'],
            ['mean()', 'Moyenne'],
            ['sd()', 'Écart-type'],
            ['zscore()', 'Score Z : (val - moyenne) / écart-type'],
            ['min()', 'Valeur minimale'],
            ['max()', 'Valeur maximale']
          ];
        @endphp
        @foreach($ops as [$symbol, $desc])
          <button 
            type="button" 
            class="bg-white hover:bg-blue-100 border border-blue-400 text-blue-700 text-xs px-2 py-1 rounded-md transition relative"
            onclick="insertFormula('{{ $symbol }}')"
            title="{{ $desc }}"
          >
            {{ $symbol }}
          </button>
        @endforeach
      </div>

      <!-- Search analyses -->
      <div class="mt-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">🔍 Rechercher une analyse :</label>
        <input 
          type="text" 
          id="analysisSearch" 
          placeholder="Code ou nom..." 
          class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
          oninput="filterAnalyses()"
        />
      </div>

        <!-- Analysis codes -->
        <div class="border-2 border-gray-300 rounded-xl p-2 mt-3 max-h-44 overflow-y-auto bg-gray-50">
          <div id="analysisButtons" class="flex flex-wrap gap-2">
            @foreach($analyses as $analysis)
              <button 
                type="button" 
                class="bg-white hover:bg-gray-100 text-xs px-2 py-1 border border-gray-300 rounded-md transition"
                onclick="insertFormula('{{ $analysis['code'] }}')"
                title="{{ $analysis['name'] ?? 'Analyse' }}"
              >
                {{ $analysis['code'] }}
              </button>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <!-- Saved formulas modal -->
    <div id="savedFormulasModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 border border-gray-300">
        <div class="flex justify-between items-center mb-3">
          <h4 class="text-lg font-semibold text-gray-800">📘 Formules enregistrées</h4>
          <button onclick="closeSavedFormulas()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div id="savedFormulasList" class="space-y-2 max-h-64 overflow-y-auto"></div>
      </div>
    </div>
  </div>

      {{-- ACTIONS --}}
      <div class="flex justify-end gap-3">
        <a href="{{ route('analyses.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
          <x-heroicon-o-x-mark class="w-5 h-5" /> Cancel
        </a>
        <button type="submit"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#bc1622] text-white hover:bg-[#a1141e] focus:ring-2 focus:ring-[#bc1622] focus:ring-offset-1">
          <x-heroicon-o-check class="w-5 h-5" /> Save Analysis
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modals --}}
@include('analyses.partials.category-modal')
@include('analyses.partials.sample-type-modal')
@include('analyses.partials.unit-modal')
<script>
const API_URL = "/api/lab-formulas"; // Adjust if needed
let savedFormulas = [];

// 🧭 Load formulas from API
async function loadFormulas() {
  try {
    const res = await fetch(API_URL);
    if (res.ok) {
      savedFormulas = await res.json();
      localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
    } else {
      console.warn("⚠️ Unable to fetch formulas from API, using local data.");
      savedFormulas = JSON.parse(localStorage.getItem('savedFormulas') || '[]');
    }
  } catch (error) {
    console.error("❌ Error fetching formulas:", error);
    savedFormulas = JSON.parse(localStorage.getItem('savedFormulas') || '[]');
  }
}

// ✍️ Formula editor helpers
function insertFormula(value) {
  const input = document.getElementById('formula');
  const start = input.selectionStart;
  const end = input.selectionEnd;
  input.setRangeText(value, start, end, 'end');
  input.focus();
  updateFormulaPreview();
}

function updateFormulaPreview() {
  const formula = document.getElementById('formula').value;
  document.getElementById('formulaDisplay').textContent = formula || '—';
}

function clearFormula() {
  document.getElementById('formula').value = '';
  updateFormulaPreview();
}

function filterAnalyses() {
  const search = document.getElementById('analysisSearch').value.toLowerCase();
  const buttons = document.querySelectorAll('#analysisButtons button');
  buttons.forEach(btn => {
    const text = btn.textContent.toLowerCase();
    btn.style.display = text.includes(search) ? 'inline-flex' : 'none';
  });
}

// 💾 Save formula to API with loader + toast
async function saveFormula() {
  const name = document.getElementById('formula_name').value.trim();
  const formula = document.getElementById('formula').value.trim();

  if (!name || !formula) {
    showToast("⚠️ Please fill in both name and formula.", "warning");
    return;
  }

  const btn = document.getElementById('saveFormulaBtn');
  btn.disabled = true;
  btn.innerHTML = `<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>Saving...`;

  try {
    const res = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, formula })
    });

    if (res.ok) {
      const saved = await res.json();
      savedFormulas.unshift(saved);
      localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
      showToast(`✅ Formula "${name}" saved successfully!`, "success");
      document.getElementById('formula_name').value = '';
      clearFormula();
    } else {
      throw new Error("API Error");
    }
  } catch (err) {
    console.warn("⚠️ API error, saving locally:", err);
    const newFormula = { name, formula, date: new Date().toISOString() };
    savedFormulas.push(newFormula);
    localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
    showToast(`✅ Formula "${name}" saved locally (offline).`, "info");
  } finally {
    btn.disabled = false;
    btn.innerHTML = "Save Formula";
  }
}

// 🗂️ Open formula modal
function openSavedFormulas() {
  const list = document.getElementById('savedFormulasList');
  list.innerHTML = '';

  if (savedFormulas.length === 0) {
    list.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No formulas saved.</p>';
  } else {
    savedFormulas.forEach((f, index) => {
      const div = document.createElement('div');
      div.className = 'p-3 border border-gray-200 rounded-md flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition';
      div.innerHTML = `
        <div>
          <p class="font-semibold text-gray-800">${f.name}</p>
          <p class="text-sm text-gray-600 font-mono">${f.formula}</p>
        </div>
        <div class="flex gap-2">
          <button onclick="useSavedFormula(${index})" class="text-indigo-600 hover:underline text-sm">Use</button>
          <button onclick="deleteSavedFormula(${index})" class="text-red-500 hover:underline text-sm">Delete</button>
        </div>
      `;
      list.appendChild(div);
    });
  }

  document.getElementById('savedFormulasModal').classList.remove('hidden');
}

function closeSavedFormulas() {
  document.getElementById('savedFormulasModal').classList.add('hidden');
}

function useSavedFormula(index) {
  const f = savedFormulas[index];
  document.getElementById('formula_name').value = f.name;
  document.getElementById('formula').value = f.formula;
  updateFormulaPreview();
  closeSavedFormulas();
}

// 🗑️ Delete from API + local
async function deleteSavedFormula(index) {
  const f = savedFormulas[index];
  if (!confirm(`Delete formula "${f.name}"?`)) return;

  try {
    if (f.id) {
      const res = await fetch(`${API_URL}/${f.id}`, { method: 'DELETE' });
      if (!res.ok) throw new Error("API Error");
    }
    savedFormulas.splice(index, 1);
    localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
    openSavedFormulas();
    showToast(`🗑️ Formula "${f.name}" deleted.`, "info");
  } catch (err) {
    console.error("Delete error:", err);
    showToast("❌ Failed to delete formula.", "error");
  }
}

document.getElementById('formula').addEventListener('input', updateFormulaPreview);
document.addEventListener('DOMContentLoaded', loadFormulas);

// 🎉 Toast notifications
function showToast(message, type = "info") {
  const colors = {
    success: "bg-green-500",
    error: "bg-red-500",
    warning: "bg-yellow-500",
    info: "bg-blue-500"
  };

  const toast = document.createElement("div");
  toast.className = `fixed bottom-4 right-4 px-4 py-2 text-white rounded-lg shadow-lg text-sm flex items-center gap-2 transition transform translate-y-2 opacity-0 ${colors[type]}`;
  toast.innerHTML = message;

  document.body.appendChild(toast);

  setTimeout(() => toast.classList.remove("translate-y-2", "opacity-0"), 100);
  setTimeout(() => {
    toast.classList.add("opacity-0");
    setTimeout(() => toast.remove(), 500);
  }, 3000);
}
</script>

<script>
let rangeCounter = 0;

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('normalRangesContainer');
    rangeCounter = container.querySelectorAll('.normal-range-item').length;
    updateNoRangesMessage();

    container.querySelectorAll('.normal-range-item').forEach(item => {
        attachSexChangeListener(item);
    });

    // Load devices
    loadCompatibleDevices();
});

function updateNoRangesMessage() {
    const container = document.getElementById('normalRangesContainer');
    const noRangesMsg = document.getElementById('noRangesMessage');
    
    if (container.children.length === 0) {
        noRangesMsg.classList.remove('hidden');
    } else {
        noRangesMsg.classList.add('hidden');
    }
}

function addNormalRange(data = {}) {
    const container = document.getElementById('normalRangesContainer');
    const index = rangeCounter++;

    const wrapper = document.createElement('div');
    wrapper.className = 'normal-range-item bg-gradient-to-br from-gray-50 to-blue-50 border-2 border-gray-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200';

    wrapper.innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sex Applicable</label>
                    <select name="normal_ranges[${index}][sex_applicable]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white sex-select text-sm">
                        <option value="All" ${data.sex_applicable === 'All' ? 'selected' : ''}>All</option>
                        <option value="M" ${data.sex_applicable === 'M' ? 'selected' : ''}>Male</option>
                        <option value="F" ${data.sex_applicable === 'F' ? 'selected' : ''}>Female</option>
                    </select>
                </div>
                <div class="pregnancy-wrapper ${data.sex_applicable === 'F' ? '' : 'hidden'}">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pregnant</label>
                    <div class="flex items-center h-10">
                        <input type="checkbox" name="normal_ranges[${index}][pregnant_applicable]" value="1" ${data.pregnant_applicable ? 'checked' : ''} 
                               class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Age Minimum</label>
                    <input type="number" name="normal_ranges[${index}][age_min]" value="${data.age_min ?? ''}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm" 
                           placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Age Maximum</label>
                    <input type="number" name="normal_ranges[${index}][age_max]" value="${data.age_max ?? ''}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm" 
                           placeholder="100">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Normal Minimum</label>
                    <input type="number" step="0.01" name="normal_ranges[${index}][normal_min]" value="${data.normal_min ?? ''}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm" 
                           placeholder="0.00">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Normal Maximum</label>
                    <input type="number" step="0.01" name="normal_ranges[${index}][normal_max]" value="${data.normal_max ?? ''}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm" 
                           placeholder="0.00">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="if (confirm('Remove this normal range?')) this.closest('.normal-range-item').remove()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 active:scale-95 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Remove
                </button>
            </div>
        </div>
    `;

    container.appendChild(wrapper);
    attachSexChangeListener(wrapper);
    updateNoRangesMessage();
}

function attachSexChangeListener(item) {
    const sexSelect = item.querySelector('.sex-select');
    const pregWrapper = item.querySelector('.pregnancy-wrapper');
    if (!sexSelect || !pregWrapper) return;

    if (sexSelect.value === 'F') pregWrapper.classList.remove('hidden');
    else pregWrapper.classList.add('hidden');

    sexSelect.addEventListener('change', () => {
        if (sexSelect.value === 'F') {
            pregWrapper.classList.remove('hidden');
        } else {
            pregWrapper.classList.add('hidden');
            const chk = pregWrapper.querySelector('input[type="checkbox"]');
            if (chk) chk.checked = false;
        }
    });
}

async function loadCompatibleDevices() {
    const container = document.getElementById('deviceSelectionContainer');
    const summary = document.getElementById('deviceSelectionSummary');
    const countSpan = document.getElementById('selectedDeviceCount');
    const apiBase = '{{ env("FASTAPI_URL") }}';
    
    try {
        const res = await fetch(`${apiBase}/lab-devices`);
        if (!res.ok) throw new Error('Device list fetch failed');
        const devices = await res.json();

        container.innerHTML = '';
        
        if (devices.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">No devices available</p>
                    <p class="text-sm">Please add laboratory devices first</p>
                </div>
            `;
            return;
        }

        // Group devices by type
        const devicesByType = devices.reduce((acc, device) => {
            const type = device.device_type || 'Other';
            if (!acc[type]) acc[type] = [];
            acc[type].push(device);
            return acc;
        }, {});

        // Create grouped checkbox lists
        Object.entries(devicesByType).forEach(([type, typeDevices]) => {
            const typeSection = document.createElement('div');
            typeSection.className = 'space-y-2';
            
            typeSection.innerHTML = `
                <h5 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <div class="h-px flex-1 bg-amber-200"></div>
                    <span>${type}</span>
                    <div class="h-px flex-1 bg-amber-200"></div>
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    ${typeDevices.map(device => `
                        <label class="flex items-start p-3 bg-white border-2 border-amber-100 rounded-lg hover:border-amber-300 hover:bg-amber-50 cursor-pointer transition-all duration-200 group">
                            <input type="checkbox" 
                                   name="device_ids[]" 
                                   value="${device.id}"
                                   onchange="updateDeviceCount()"
                                   class="mt-0.5 h-4 w-4 text-amber-600 border-gray-300 rounded focus:ring-2 focus:ring-amber-500 transition">
                            <div class="ml-3 flex-1">
                                <span class="text-sm font-medium text-gray-900 group-hover:text-amber-900">${device.name}</span>
                                <p class="text-xs text-gray-500 mt-0.5">${device.model || 'Unknown Model'}</p>
                            </div>
                        </label>
                    `).join('')}
                </div>
            `;
            
            container.appendChild(typeSection);
        });

        summary.classList.remove('hidden');
        updateDeviceCount();

    } catch (err) {
        console.error(err);
        container.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <svg class="w-10 h-10 mx-auto mb-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-red-800">Failed to load devices</p>
                <p class="text-xs text-red-600 mt-1">Please refresh the page to try again</p>
            </div>
        `;
    }
}
function updateDeviceCount() {
    const checkboxes = document.querySelectorAll('input[name="compatible_device_ids[]"]:checked');
    const countSpan = document.getElementById('selectedDeviceCount');
    countSpan.textContent = checkboxes.length;
}
// Modal Functions
function openCategoryModal() { document.getElementById('categoryModal').classList.remove('hidden'); }
function closeCategoryModal() { 
    document.getElementById('categoryModal').classList.add('hidden');
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryError').textContent = '';
}

function openSampleTypeModal() { document.getElementById('sampleTypeModal').classList.remove('hidden'); }
function closeSampleTypeModal() { 
    document.getElementById('sampleTypeModal').classList.add('hidden');
    document.getElementById('sampleTypeName').value = '';
    document.getElementById('sampleTypeError').textContent = '';
}

function openUnitModal() { document.getElementById('unitModal').classList.remove('hidden'); }
function closeUnitModal() { 
    document.getElementById('unitModal').classList.add('hidden');
    document.getElementById('unitName').value = '';
    document.getElementById('unitError').textContent = '';
}

// Temporary storage
let tempCategories = [];
let tempSampleTypes = [];
let tempUnits = [];
let tempIdCounter = -1;

// Form submissions
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('categoryName').value.trim();
    const errorDiv = document.getElementById('categoryError');
    
    if (!name) {
        errorDiv.textContent = 'Category name is required';
        return;
    }
    
    const select = document.getElementById('category_analyse_id');
    const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());
    if (existingOptions.includes(name.toLowerCase())) {
        errorDiv.textContent = 'Category already exists';
        return;
    }
    
    const tempCategory = { id: tempIdCounter--, name: name, isTemp: true };
    tempCategories.push(tempCategory);
    
    const option = new Option(name + ' (New)', tempCategory.id, true, true);
    option.className = 'text-emerald-600 font-medium';
    select.add(option);
    
    closeCategoryModal();
});

document.getElementById('sampleTypeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('sampleTypeName').value.trim();
    const errorDiv = document.getElementById('sampleTypeError');
    
    if (!name) {
        errorDiv.textContent = 'Sample type name is required';
        return;
    }
    
    const select = document.getElementById('sample_type_id');
    const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());
    if (existingOptions.includes(name.toLowerCase())) {
        errorDiv.textContent = 'Sample type already exists';
        return;
    }
    
    const tempSampleType = { id: tempIdCounter--, name: name, isTemp: true };
    tempSampleTypes.push(tempSampleType);
    
    const option = new Option(name + ' (New)', tempSampleType.id, true, true);
    option.className = 'text-emerald-600 font-medium';
    select.add(option);
    
    closeSampleTypeModal();
});

document.getElementById('unitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('unitName').value.trim();
    const errorDiv = document.getElementById('unitError');
    
    if (!name) {
        errorDiv.textContent = 'Unit name is required';
        return;
    }
    
    const select = document.getElementById('unit_id');
    const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());
    if (existingOptions.includes(name.toLowerCase())) {
        errorDiv.textContent = 'Unit already exists';
        return;
    }
    
    const tempUnit = { id: tempIdCounter--, name: name, isTemp: true };
    tempUnits.push(tempUnit);
    
    const option = new Option(name + ' (New)', tempUnit.id, true, true);
    option.className = 'text-emerald-600 font-medium';
    select.add(option);
    
    closeUnitModal();
});

// Main form submission
document.querySelector('form[action="{{ route('analyses.store') }}"]').addEventListener('submit', function(e) {
    tempCategories.forEach(category => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'temp_categories[]';
        input.value = JSON.stringify(category);
        this.appendChild(input);
    });
    
    tempSampleTypes.forEach(sampleType => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'temp_sample_types[]';
        input.value = JSON.stringify(sampleType);
        this.appendChild(input);
    });
    
    tempUnits.forEach(unit => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'temp_units[]';
        input.value = JSON.stringify(unit);
        this.appendChild(input);
    });
});

// Close modals on outside click
document.addEventListener('click', function(e) {
    if (e.target.id === 'categoryModal') closeCategoryModal();
    if (e.target.id === 'sampleTypeModal') closeSampleTypeModal();
    if (e.target.id === 'unitModal') closeUnitModal();
});
</script>
@endsection
