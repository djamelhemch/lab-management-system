{{-- resources/views/analyses/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add New Analysis')


@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
  <div class="max-w-5xl mx-auto">
<button type="button"
        onclick="confirmClearAll()"
        class="fixed bottom-5 right-8 z-50
               inline-flex items-center gap-2
               px-4 py-2
               bg-white text-red-600
               border border-red-200
               rounded-lg
               text-xs font-semibold
               shadow-md
               hover:bg-red-50 hover:border-red-300
               active:scale-95 transition-all duration-200">

    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>

    <span>Vider les champs</span>
</button>

    {{-- Page Header --}}
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Analysis</h1>
          <p class="text-sm text-gray-600 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Define a new laboratory analysis including its category, unit, pricing, and device compatibility.
          </p>
        </div>
        <a href="{{ route('analyses.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Back to List
        </a>
      </div>
    </div>

    <form id="analysisCreateForm" action="{{ route('analyses.store') }}" method="POST" class="space-y-6" novalidate>
      @csrf

      {{-- BASIC INFORMATION --}}
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Basic Information
          </h2>
        </div>

        <div class="p-6 space-y-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Code --}}
            <div>
              <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Analysis Code</label>
              <p class="text-xs text-gray-500 mb-2">Internal reference code (optional)</p>
              <input id="code" name="code" value="{{ old('code') }}" type="text" placeholder="e.g., CBC001"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" />
              @error('code')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Name --}}
            <div>
              <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Analysis Name <span class="text-red-500">*</span></label>
              <p class="text-xs text-gray-500 mb-2">Short, descriptive name shown to clinicians</p>
              <input id="name" name="name" value="{{ old('name') }}" type="text" required placeholder="e.g., Complete Blood Count"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" />
              @error('name')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Category --}}
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
              <div class="flex gap-2">
                <select name="category_analyse_id" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                  <option value="">Select category</option>
                  @foreach($categories as $category)
                    <option value="{{ $category['id'] }}" {{ old('category_analyse_id') == $category['id'] ? 'selected' : '' }}>
                      {{ $category['name'] }}
                    </option>
                  @endforeach
                </select>
                <button type="button" onclick="openCategoryModal()"
                  class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-all">
                  <x-heroicon-o-plus-small class="w-5 h-5" />
                </button>
              </div>
            </div>

            {{-- Price --}}
            <div>
              <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Price <span class="text-red-500">*</span></label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">DZD</span>
                <input id="price" name="price" type="number" step="0.01" min="0"
                  value="{{ old('price') }}" class="w-full pl-16 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white"
                  placeholder="0.00" />
              </div>
            </div>

            {{-- Unit --}}
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
              <div class="flex gap-2">
                <select name="unit_id" id="unit_select" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                  <option value="">Select unit</option>
                  @foreach($units as $unit)
                    <option value="{{ $unit['id'] }}" {{ old('unit_id') == $unit['id'] ? 'selected' : '' }}>
                      {{ $unit['name'] }}
                    </option>
                  @endforeach
                </select>
                <button type="button" onclick="openUnitModal()"
                  class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-all">
                  <x-heroicon-o-plus-small class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- SPECIFICATIONS --}}
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Specifications
          </h2>
        </div>

        <div class="p-6 space-y-6">
          {{-- Reference Ranges --}}
          <div>
            <div class="flex justify-between items-center mb-3">
              <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Normal Reference Ranges
              </h3>
              <button type="button" onclick="addNormalRange()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Range
              </button>
            </div>
            <div id="normalRangesContainer" class="space-y-3"></div>
            <div id="noRangesMessage" class="text-center py-8 text-gray-500 text-sm border-2 border-dashed border-gray-300 rounded-lg mt-3 bg-gray-50">
              No ranges defined yet. Click "Add Range" to begin.
            </div>
          </div>

          {{-- Sample Requirements --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-6 border-t border-gray-200">
<div class="p-6">
        <div class="max-w-md">
            <label for="sample_type_ids" class="block text-sm font-semibold text-gray-700 mb-2">
                Types d'Échantillon <span class="text-red-500">*</span>
            </label>

            <div class="flex gap-2 relative">
                <!-- Chips container -->
                <div id="chips-container" 
                     class="flex-1 flex flex-wrap items-center gap-1.5 px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 hover:bg-white focus-within:ring-2 focus-within:ring-purple-500 transition-all duration-200 cursor-text min-h-[48px]">
                     
                    <div id="selected-chips" class="flex flex-wrap gap-1.5"></div>
                    <span id="placeholder" class="text-gray-400 select-none">Sélectionnez les types d'échantillon...</span>

                    <!-- Hidden select -->
                    <select name="sample_type_ids[]" 
                            id="sample_type_ids"
                            multiple 
                            class="hidden">
                        @foreach($sampleTypes as $type)
                            <option value="{{ $type['id'] }}"
                                {{ in_array($type['id'], old('sample_type_ids', [])) ? 'selected' : '' }}>
                                {{ $type['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Add new sample type button -->
                <button type="button" 
                        onclick="openSampleTypeModal()"
                        title="Ajouter un nouveau type d'échantillon"
                        class="flex-shrink-0 px-3 py-2.5 bg-purple-50 border border-purple-200 text-purple-600 rounded-lg hover:bg-purple-100 hover:border-purple-300 transition-all duration-200 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <x-heroicon-o-plus-small class="w-5 h-5" />
                </button>
            </div>

            <p class="mt-2 text-xs text-gray-500 flex items-start gap-1">
                <span class="flex-shrink-0">ℹ️</span>
                <span>Vous pouvez sélectionner plusieurs types d'échantillon</span>
            </p>

            @error('sample_type_ids')
                <p class="mt-2 text-sm text-red-600 flex items-start gap-1">
                    <span class="flex-shrink-0">⚠️</span>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>
    </div>

            {{-- Tube Type --}}
            <div class="p-6">
              <label for="tube_type" class="block text-sm font-semibold text-gray-700 mb-2">Tube Type</label>
              <select id="tube_type" name="tube_type"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                <option value="">Select tube type</option>
                <option value="EDTA">EDTA (Lavender)</option>
                <option value="Heparin">Heparin (Green)</option>
                <option value="Serum">Serum (Red/Gold)</option>
                <option value="Citrate">Citrate (Blue)</option>
                <option value="Fluoride">Fluoride (Gray)</option>
                <option value="Plain">Plain (Red)</option>
              </select>
            </div>
          </div>

          {{-- Compatible Devices --}}
          <div class="pt-6 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
              </svg>
              Compatible Devices
            </h3>
            <div id="deviceSelectionContainer" class="border-2 border-gray-200 divide-y divide-gray-200 rounded-lg bg-gray-50">
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
      </div>

      {{-- FORMULA SECTION --}}
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
              🧮 Formule de calcul
            </h2>
            <button type="button" onclick="openSavedFormulas()"
              class="text-sm text-indigo-600 hover:underline">
              📚 Formules enregistrées
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Formula Name -->
          <div class="mb-5">
            <label for="formula_name" class="block text-sm font-semibold text-gray-700 mb-2">
              Nom de la formule :
            </label>
            <input type="text" id="formula_name" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
              placeholder="Ex : Formule de Friedewald">
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LEFT: Editor -->
            <div>
              <label for="formula" class="block text-sm font-semibold text-gray-700 mb-2">
                Expression mathématique :
              </label>
              <div class="border-2 border-blue-400 rounded-xl p-3 bg-blue-50 focus-within:ring-2 focus-within:ring-blue-300 transition">
                <textarea id="formula" rows="4" 
                  class="w-full bg-transparent outline-none text-gray-800 text-sm font-mono"
                  placeholder="Exemple : LDL-C = CT - HDL-C - TG / 5"></textarea>
              </div>

              <div id="formulaPreview" class="mt-3 text-sm font-mono text-gray-600 bg-gray-100 border border-gray-300 rounded-lg p-2">
                🧠 Aperçu : <span id="formulaDisplay" class="text-gray-800"></span>
              </div>

              <div class="flex gap-2 mt-3">
                <button type="button" onclick="clearFormula()"
                  class="bg-red-50 border border-red-300 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition text-sm">
                  Effacer
                </button>
                <button id="saveFormulaBtn" type="button" onclick="saveFormula()"
                  class="bg-green-50 border border-green-300 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-100 transition text-sm">
                  💾 Enregistrer la formule
                </button>
              </div>
            </div>

            <!-- RIGHT: Tools -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">🔢 Opérateurs et fonctions :</label>
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
                  <button type="button" 
                    class="bg-white hover:bg-blue-100 border border-blue-400 text-blue-700 text-xs px-2 py-1 rounded-md transition relative"
                    onclick="insertFormula('{{ $symbol }}')"
                    title="{{ $desc }}">
                    {{ $symbol }}
                  </button>
                @endforeach
              </div>

              <!-- Search analyses -->
              <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">🔍 Rechercher une analyse :</label>
                <input type="text" id="analysisSearch" placeholder="Code ou nom..." 
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-gray-50 hover:bg-white"
                  oninput="filterAnalyses()" />
              </div>

              <!-- Analysis codes -->
              <div class="border-2 border-gray-300 rounded-xl p-2 mt-3 max-h-44 overflow-y-auto bg-gray-50">
                <div id="analysisButtons" class="flex flex-wrap gap-2">
                  @foreach($analyses as $analysis)
                    <button type="button" 
                      class="bg-white hover:bg-gray-100 text-xs px-2 py-1 border border-gray-300 rounded-md transition"
                      onclick="insertFormula('{{ $analysis['code'] }}')"
                      title="{{ $analysis['name'] ?? 'Analyse' }}">
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
      </div>

      {{-- ACTIONS --}}
      <div class="flex items-center justify-end gap-3 pt-4">
        <a href="{{ route('analyses.index') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 hover:shadow-md transition-all duration-200 font-medium">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          Cancel
        </a>
        <button type="submit" 
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 hover:shadow-lg transition-all duration-200 font-medium">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Create Analysis
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modals --}}
@include('analyses.partials.category-modal')
@include('analyses.partials.sample-type-modal')
@include('analyses.partials.unit-modal')
<style>
/* Chips styling */
#selected-chips > div {
    @apply inline-flex items-center gap-2 px-3 py-1.5 bg-purple-50 text-purple-700 text-sm font-medium rounded-full border border-purple-200 shadow-sm transition-all duration-200;
}
#selected-chips > div:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(128, 90, 213, 0.25);
}
#selected-chips > div button {
    @apply w-6 h-6 flex items-center justify-center rounded-full text-sm text-purple-700 bg-purple-100 hover:bg-red-500 hover:text-white transition-all;
}
@keyframes slideOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(-10px); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('sample_type_ids');
    const chipsContainer = document.getElementById('selected-chips');
    const placeholder = document.getElementById('placeholder');
    const container = document.getElementById('chips-container');

    const updatePlaceholder = () => placeholder.style.display = chipsContainer.children.length ? 'none' : 'block';

    const addChip = (text, value) => {
        if ([...chipsContainer.children].some(chip => chip.dataset.value === value)) return;

        const chip = document.createElement('div');
        chip.className = 'inline-flex items-center gap-2 px-3 py-1.5 bg-purple-50 text-purple-700 text-sm font-medium rounded-full border border-purple-200 shadow-sm transition-all duration-200';
        chip.dataset.value = value;
        chip.innerHTML = `<span>${text}</span><button type="button">&times;</button>`;

        // Make X button bigger and easier to click
        chip.querySelector('button').onclick = () => {
            chip.style.animation = 'slideOut 0.2s ease forwards';
            setTimeout(() => {
                chip.remove();
                select.querySelector(`option[value="${value}"]`).selected = false;
                updatePlaceholder();
            }, 200);
        };

        chipsContainer.appendChild(chip);
        select.querySelector(`option[value="${value}"]`).selected = true;
        updatePlaceholder();
    };

    // Initialize chips
    Array.from(select.selectedOptions).forEach(option => addChip(option.textContent.trim(), option.value));

    // Simple dropdown
    container.addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON') return;

        let dropdown = document.createElement('div');
        dropdown.id = 'multi-select-dropdown';
        dropdown.className = 'absolute top-full left-0 right-0 z-50 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1';
        dropdown.style.minWidth = '100%';

        Array.from(select.options).forEach(option => {
            const item = document.createElement('div');
            item.className = 'px-4 py-2 hover:bg-purple-50 cursor-pointer flex items-center gap-2';
            item.textContent = option.textContent;
            if (option.selected) item.classList.add('bg-purple-50', 'font-medium');

            item.addEventListener('click', () => {
                option.selected = !option.selected;
                if (option.selected) {
                    addChip(option.textContent, option.value);
                    item.classList.add('bg-purple-50', 'font-medium');
                } else {
                    const chip = chipsContainer.querySelector(`div[data-value="${option.value}"]`);
                    if (chip) chip.remove();
                    item.classList.remove('bg-purple-50', 'font-medium');
                }
                updatePlaceholder();
            });

            dropdown.appendChild(item);
        });

        // Remove old dropdown if exists
        const oldDropdown = document.getElementById('multi-select-dropdown');
        if (oldDropdown) oldDropdown.remove();
        container.appendChild(dropdown);
    });

    // Close dropdown on outside click
    document.addEventListener('click', e => {
        if (!container.contains(e.target)) {
            const dropdown = document.getElementById('multi-select-dropdown');
            if (dropdown) dropdown.remove();
        }
    });

    updatePlaceholder();
});
</script>
<script>
// ========================================
// AUTO-SAVE & RECOVERY SYSTEM WITH LOGGING
// ========================================

const AUTOSAVE_KEY = 'analysis_form_autosave';
const AUTOSAVE_INTERVAL = 3000; // Save every 3 seconds
let autosaveTimer = null;
let saveCount = 0;
let hasRestoredOnce = false; // ✅ Prevent multiple restore prompts

// ✅ Check if form has meaningful data
function hasFormData() {
    const name = document.getElementById('name')?.value || '';
    const code = document.getElementById('code')?.value || '';
    const price = document.getElementById('price')?.value || '';
    const formula = document.getElementById('formula')?.value || '';
    const rangeItems = document.querySelectorAll('.normal-range-item');
    
    // Consider form has data if ANY of these fields are filled
    return name.trim() !== '' || 
           code.trim() !== '' || 
           price.trim() !== '' || 
           formula.trim() !== '' ||
           rangeItems.length > 0;
}

// Save form data to localStorage
function saveFormData() {
    // ✅ Only save if form has actual data
    if (!hasFormData()) {
        console.log('ℹ️ Form is empty, skipping autosave');
        return;
    }
    
    saveCount++;
    console.log(`%c🔄 [${saveCount}] Starting autosave...`, 'color: #3b82f6; font-weight: bold');
    
    try {
        const formData = {
            // Basic fields
            name: document.getElementById('name')?.value || '',
            code: document.getElementById('code')?.value || '',
            category_analyse_id: document.getElementById('category_analyse_id')?.value || '',
            sample_type_id: document.getElementById('sample_type_id')?.value || '',
            unit_id: document.getElementById('unit_id')?.value || '',
            formula: document.getElementById('formula')?.value || '',
            price: document.getElementById('price')?.value || '',
            tube_type: document.getElementById('tube_type')?.value || '',
            is_active: document.getElementById('is_active')?.checked || false,
            
            // Device IDs
            device_ids: Array.from(document.querySelectorAll('input[name="device_ids[]"]:checked'))
                .map(cb => cb.value),
            
            // Normal ranges
            normal_ranges: [],
            
            timestamp: new Date().toISOString()
        };
        
        // Extract all normal ranges
        const rangeItems = document.querySelectorAll('.normal-range-item');
        
        rangeItems.forEach((range, index) => {
            const rangeData = {
                sex_applicable: range.querySelector(`select[name*="[sex_applicable]"]`)?.value || 'All',
                
                age_min_years: range.querySelector(`input[name*="[age_min_years]"]`)?.value || '',
                age_min_months: range.querySelector(`input[name*="[age_min_months]"]`)?.value || '',
                age_min_days: range.querySelector(`input[name*="[age_min_days]"]`)?.value || '',
                
                age_max_years: range.querySelector(`input[name*="[age_max_years]"]`)?.value || '',
                age_max_months: range.querySelector(`input[name*="[age_max_months]"]`)?.value || '',
                age_max_days: range.querySelector(`input[name*="[age_max_days]"]`)?.value || '',
                
                normal_min: range.querySelector(`input[name*="[normal_min]"]`)?.value || '',
                normal_max: range.querySelector(`input[name*="[normal_max]"]`)?.value || '',
                
                pregnant_applicable: range.querySelector(`input[name*="[pregnant_applicable]"]`)?.checked || false
            };
            
            formData.normal_ranges.push(rangeData);
        });
        
        // Calculate size
        const dataString = JSON.stringify(formData);
        const sizeKB = (dataString.length / 1024).toFixed(2);
        
        localStorage.setItem(AUTOSAVE_KEY, dataString);
        
        console.log(`%c✅ [${saveCount}] Autosave successful! (${sizeKB} KB)`, 'color: #10b981; font-weight: bold');
        
        // Visual feedback
        showAutosaveIndicator('success');
        
    } catch (error) {
        console.error('%c❌ Autosave FAILED!', 'color: #ef4444; font-weight: bold', error);
        showAutosaveIndicator('error');
    }
}

// Visual indicator
function showAutosaveIndicator(status = 'success') {
    let indicator = document.getElementById('autosave-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'autosave-indicator';
        document.body.appendChild(indicator);
    }
    
    if (status === 'success') {
        indicator.className = 'fixed bottom-20 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium transition-opacity duration-300 z-50';
        indicator.innerHTML = '💾 Sauvegardé';
    } else {
        indicator.className = 'fixed bottom-20 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium transition-opacity duration-300 z-50';
        indicator.innerHTML = '❌ Erreur';
    }
    
    indicator.style.opacity = '1';
    
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 1500);
}

// ✅ Beautiful restore modal with SweetAlert2 style
function showRestoreModal(data, timeSaved) {
    const modal = document.createElement('div');
    modal.id = 'restore-modal';
    modal.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-slideUp">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-t-2xl text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Données sauvegardées trouvées !</h2>
                <p class="text-blue-100 text-sm mt-2">Voulez-vous restaurer votre travail ?</p>
            </div>
            
            <!-- Content -->
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Dernière sauvegarde</p>
                            <p class="text-sm font-bold text-gray-800">${timeSaved}</p>
                        </div>
                    </div>
                    
                    ${data.name ? `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Nom de l'analyse</p>
                            <p class="text-sm font-bold text-gray-800">${data.name}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${data.code ? `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Code</p>
                            <p class="text-sm font-bold text-gray-800">${data.code}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Plages normales</p>
                            <p class="text-sm font-bold text-gray-800">${data.normal_ranges?.length || 0} plage(s)</p>
                        </div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 text-center italic">
                    💡 Vos données sont enregistrées localement et peuvent être restaurées à tout moment
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-3 p-6 pt-0">
                <button onclick="declineRestore()" 
                    class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all">
                    ❌ Ignorer
                </button>
                <button onclick="acceptRestore()" 
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl">
                    ✅ Restaurer
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        .animate-slideUp { animation: slideUp 0.4s ease-out; }
    `;
    document.head.appendChild(style);
}

// Accept restore
window.acceptRestore = function() {
    const modal = document.getElementById('restore-modal');
    if (modal) modal.remove();
    
    try {
        const savedData = localStorage.getItem(AUTOSAVE_KEY);
        const data = JSON.parse(savedData);
        
        // Restore data
        if (data.name) document.getElementById('name').value = data.name;
        if (data.code) document.getElementById('code').value = data.code;
        if (data.category_analyse_id) document.getElementById('category_analyse_id').value = data.category_analyse_id;
        if (data.sample_type_id) document.getElementById('sample_type_id').value = data.sample_type_id;
        if (data.unit_id) {
            document.getElementById('unit_id').value = data.unit_id;
            const unitSelect = document.getElementById('unit_id');
            if (unitSelect && typeof currentUnit !== 'undefined') {
                currentUnit = unitSelect.options[unitSelect.selectedIndex]?.text || '';
            }
        }
        if (data.formula) document.getElementById('formula').value = data.formula;
        if (data.price) document.getElementById('price').value = data.price;
        if (data.tube_type) document.getElementById('tube_type').value = data.tube_type;
        if (data.is_active) document.getElementById('is_active').checked = data.is_active;
        
        // Restore device selections
        if (data.device_ids && data.device_ids.length > 0) {
            document.querySelectorAll('input[name="device_ids[]"]').forEach(checkbox => {
                if (data.device_ids.includes(checkbox.value)) {
                    checkbox.checked = true;
                }
            });
        }
        
        // Restore normal ranges
        if (data.normal_ranges && data.normal_ranges.length > 0) {
            document.querySelectorAll('.normal-range-item').forEach(item => item.remove());
            data.normal_ranges.forEach((rangeData) => {
                addNormalRange(rangeData);
            });
        }
        
        showToast('✅ Données restaurées avec succès!', 'success');
        
    } catch (error) {
        console.error('Restore failed:', error);
        showToast('❌ Erreur lors de la restauration', 'error');
    }
};

// Decline restore
// Decline restore - Clear all fields
window.declineRestore = function() {
    const modal = document.getElementById('restore-modal');
    if (modal) modal.remove();
    
    // Clear autosave
    localStorage.removeItem(AUTOSAVE_KEY);
    console.log('❌ User declined restoration - clearing all fields');
    
    // Execute clear all
    executeClearAll(true); // Pass true to skip confirmation modal
};

// Restore form data from localStorage
function restoreFormData() {
    // ✅ Only show modal once per page load
    if (hasRestoredOnce) {
        return false;
    }
    
    try {
        const savedData = localStorage.getItem(AUTOSAVE_KEY);
        if (!savedData) {
            return false;
        }
        
        const data = JSON.parse(savedData);
        const timeSaved = new Date(data.timestamp).toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        hasRestoredOnce = true;
        showRestoreModal(data, timeSaved);
        
        return true;
    } catch (error) {
        console.error('Restore check failed:', error);
        return false;
    }
}

// Clear autosave
function clearAutosave() {
    localStorage.removeItem(AUTOSAVE_KEY);
    console.log('🗑️ Autosave data cleared');
    showToast('🗑️ Sauvegarde automatique effacée', 'info');
}

// Start auto-save timer
function startAutosave() {
    const form = document.getElementById('analysisCreateForm');
    if (!form) return;
    
    // Save on input change (debounced)
    form.addEventListener('input', (e) => {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(saveFormData, AUTOSAVE_INTERVAL);
    });
    
    form.addEventListener('change', (e) => {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(saveFormData, 1000);
    });
    
    // Save before page unload
    window.addEventListener('beforeunload', () => {
        if (hasFormData()) {
            saveFormData();
        }
    });
    
    console.log('✅ Autosave enabled');
}

// Clear autosave on successful submission
function handleFormSubmit(event) {
    setTimeout(() => {
        clearAutosave();
    }, 1000);
}

// ========================================
// INITIALIZE ON PAGE LOAD
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('%c🚀 Autosave System Ready', 'color: #10b981; font-weight: bold; font-size: 14px');
    
    // Try to restore data first (only if saved data exists)
    restoreFormData();
    
    // Start autosave system
    startAutosave();
    
    // Attach to form submit
    const form = document.getElementById('analysisCreateForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
});
</script>
{{-- FORMULA MANAGEMENT SCRIPT --}}
<script>
// ✅ FIXED: Use route() helper for HTTPS URL
const API_URL = "{{ route('lab.formulas') }}";
let savedFormulas = [];

// 🧭 Load formulas from API
async function loadFormulas() {
  try {
    const res = await fetch(API_URL, {
      headers: {
        'Accept': 'application/json'
      }
    });
    
    if (res.ok) {
      savedFormulas = await res.json();
      localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
      console.log('✅ Formulas loaded:', savedFormulas.length);
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
  document.getElementById('formula_name').value = '';
  updateFormulaPreview();
}

function filterAnalyses() {
  const search = document.getElementById('analysisSearch').value.toLowerCase();
  const buttons = document.querySelectorAll('#analysisButtons button');
  buttons.forEach(btn => {
    const text = btn.textContent.toLowerCase();
    const title = (btn.title || '').toLowerCase();
    btn.style.display = (text.includes(search) || title.includes(search)) ? 'inline-flex' : 'none';
  });
}

// 💾 Save formula to API
async function saveFormula() {
  const name = document.getElementById('formula_name').value.trim();
  const formula = document.getElementById('formula').value.trim();

  if (!name || !formula) {
    showToast("⚠️ Please fill in both name and formula.", "warning");
    return;
  }

  const btn = document.getElementById('saveFormulaBtn');
  btn.disabled = true;
  btn.innerHTML = `<svg class="animate-spin h-4 w-4 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>Saving...`;

  try {
    const res = await fetch(API_URL, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ name, formula })
    });

    if (res.ok) {
      const saved = await res.json();
      savedFormulas.unshift(saved);
      localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
      showToast(`✅ Formula "${name}" saved successfully!`, "success");
      clearFormula();
    } else {
      const error = await res.json();
      throw new Error(error.detail || "API Error");
    }
  } catch (err) {
    console.warn("⚠️ API error, saving locally:", err);
    const newFormula = { 
      id: Date.now(), 
      name, 
      formula, 
      created_at: new Date().toISOString() 
    };
    savedFormulas.unshift(newFormula);
    localStorage.setItem('savedFormulas', JSON.stringify(savedFormulas));
    showToast(`✅ Formula "${name}" saved locally (offline).`, "info");
  } finally {
    btn.disabled = false;
    btn.innerHTML = "💾 Enregistrer la formule";
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
        <div class="flex-1">
          <p class="font-semibold text-gray-800">${f.name}</p>
          <p class="text-sm text-gray-600 font-mono break-all">${f.formula}</p>
        </div>
        <div class="flex gap-2 ml-3">
          <button onclick="useSavedFormula(${index})" class="text-indigo-600 hover:underline text-sm whitespace-nowrap">Use</button>
          <button onclick="deleteSavedFormula(${index})" class="text-red-500 hover:underline text-sm whitespace-nowrap">Delete</button>
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
  showToast(`📋 Formula "${f.name}" loaded.`, "info");
}

// 🗑️ Delete formula
async function deleteSavedFormula(index) {
  const f = savedFormulas[index];
  if (!confirm(`Delete formula "${f.name}"?`)) return;

  try {
    if (f.id && f.id > 0) {
      const res = await fetch(`${API_URL}/${f.id}`, { 
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
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

// 🎉 Toast notifications
function showToast(message, type = "info") {
  const colors = {
    success: "bg-green-500",
    error: "bg-red-500",
    warning: "bg-yellow-500",
    info: "bg-blue-500"
  };

  const toast = document.createElement("div");
  toast.className = `fixed bottom-4 right-4 px-4 py-3 text-white rounded-lg shadow-lg text-sm flex items-center gap-2 transition transform translate-y-2 opacity-0 z-50 ${colors[type]}`;
  toast.innerHTML = message;

  document.body.appendChild(toast);

  setTimeout(() => toast.classList.remove("translate-y-2", "opacity-0"), 100);
  setTimeout(() => {
    toast.classList.add("opacity-0", "translate-y-2");
    setTimeout(() => toast.remove(), 500);
  }, 3000);
}
</script>
<script>
function confirmClearAll() {
    const modal = document.createElement('div');
    modal.id = 'clear-all-modal';
    modal.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 rounded-t-2xl text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Effacer tous les champs ?</h2>
                <p class="text-red-100 text-sm mt-2">Cette action ne peut pas être annulée</p>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800 text-center font-medium">
                        ⚠️ Tous les champs seront vidés, y compris les plages normales
                    </p>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-3 p-6 pt-0">
                <button onclick="closeClearModal()" 
                    class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all">
                    ✖️ Annuler
                </button>
                <button onclick="executeClearAll(false)" 
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl">
                    🗑️ Tout Effacer
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function closeClearModal() {
    const modal = document.getElementById('clear-all-modal');
    if (modal) {
        modal.remove();
    }
}

// ✅ SINGLE executeClearAll function - ACCURATE counting
function executeClearAll(silent = false) {
    if (!silent) {
        closeClearModal();
    }
    
    const form = document.getElementById('analysisCreateForm');
    if (!form) {
        console.error('Form not found!');
        return;
    }
    
    let clearedCount = 0;
    
    // 1. Clear text inputs (ONLY if they have non-empty values)
    form.querySelectorAll('input[type="text"]').forEach(input => {
        if (input.value && input.value.trim() !== '') {
            console.log('Clearing text input:', input.name, 'value:', input.value);
            input.value = '';
            clearedCount++;
        }
    });
    
    // 2. Clear number inputs (ONLY if they have non-empty values)
    form.querySelectorAll('input[type="number"]').forEach(input => {
        if (input.value && input.value.trim() !== '') {
            console.log('Clearing number input:', input.name, 'value:', input.value);
            input.value = '';
            clearedCount++;
        }
    });
    
    // 3. Clear textareas (ONLY if they have non-empty values)
    form.querySelectorAll('textarea').forEach(textarea => {
        if (textarea.value && textarea.value.trim() !== '') {
            console.log('Clearing textarea:', textarea.name, 'value:', textarea.value);
            textarea.value = '';
            clearedCount++;
        }
    });
    
    // 4. Reset selects ONLY if they're NOT on the first option (index 0)
    form.querySelectorAll('select').forEach(select => {
        if (select.selectedIndex > 0) {
            console.log('Resetting select:', select.name, 'from index:', select.selectedIndex);
            select.selectedIndex = 0;
            clearedCount++;
        }
    });
    
    // 5. Uncheck checkboxes ONLY if they're checked (except is_active)
    form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        if (checkbox.id !== 'is_active' && checkbox.checked) {
            console.log('Unchecking checkbox:', checkbox.name);
            checkbox.checked = false;
            clearedCount++;
        }
    });
    
    // 6. Remove normal ranges (count them)
    const normalRanges = document.querySelectorAll('.normal-range-item');
    if (normalRanges.length > 0) {
        console.log('Removing', normalRanges.length, 'normal ranges');
        normalRanges.forEach(range => range.remove());
        clearedCount += normalRanges.length;
    }
    
    // 7. Update "no ranges" message
    const noRangesMsg = document.getElementById('noRangesMessage');
    if (noRangesMsg) {
        noRangesMsg.classList.remove('hidden');
    }
    
    // 8. Clear localStorage autosave
    if (typeof AUTOSAVE_KEY !== 'undefined') {
        localStorage.removeItem(AUTOSAVE_KEY);
    }
    
    // 9. Clear formula preview
    const formulaDisplay = document.getElementById('formulaDisplay');
    if (formulaDisplay && formulaDisplay.textContent && formulaDisplay.textContent.trim() !== '' && formulaDisplay.textContent !== '—') {
        formulaDisplay.textContent = '—';
    }
    
    console.log(`✅ Total cleared: ${clearedCount} items`);
    
    // Show accurate toast message (if not silent)
    if (!silent && typeof showToast === 'function') {
        if (clearedCount === 0) {
            showToast('ℹ️ Aucun champ à effacer', 'info');
        } else if (clearedCount === 1) {
            showToast('🗑️ 1 champ effacé', 'success');
        } else {
            showToast(`🗑️ ${clearedCount} champs effacés`, 'success');
        }
    }
}

// Test button functionality on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Clear button script loaded');
    
    const form = document.getElementById('analysisCreateForm');
    if (form) {
        console.log('✅ Form found');
    } else {
        console.error('❌ Form NOT found! Make sure your form has id="analysisCreateForm"');
    }
});
</script>
{{-- NORMAL RANGES MANAGEMENT SCRIPT --}}
<script>
// ✅ GLOBAL SCOPE - Functions accessible from onclick attributes
let rangeCounter = 0;
let tempCategories = [];
let tempSampleTypes = [];
let tempUnits = [];
let tempIdCounter = -1;

// ✅ Update range badges (#1, #2, ...)
function updateRangeNumbers() {
    const items = document.querySelectorAll('.normal-range-item');
    items.forEach((item, i) => {
        const badge = item.querySelector('.range-badge');
        if (badge) badge.textContent = i + 1;
    });
}

// ✅ Show "Add Another Range" only on the last item
function updateAddButtons() {
    const items = document.querySelectorAll('.normal-range-item');
    items.forEach((item, i) => {
        const addBtn = item.querySelector('.bg-\\[\\#bc1622\\]');
        if (addBtn) {
            addBtn.style.display = (i === items.length - 1) ? 'inline-flex' : 'none';
        }
    });
}
function addNormalRange(data = {}) {
    const container = document.getElementById('normalRangesContainer');
    if (!container) return;

    const index = rangeCounter++;

    const wrapper = document.createElement('div');
    wrapper.className = 'normal-range-item bg-gradient-to-r from-gray-50 to-blue-50 border-2 border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200';
    wrapper.dataset.rangeIndex = index;

    wrapper.innerHTML = `
        <!-- Range Counter Header -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-gray-300">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-sm rounded-full shadow-md range-number">
                    #1
                </span>
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Plage Normale</h3>
            </div>
            <button type="button" onclick="addNormalRange()"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-600 hover:text-white transition-all duration-200 text-xs font-medium border border-green-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
            
            <!-- Sex -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Sexe</label>
                <select name="normal_ranges[${index}][sex_applicable]" class="sex-select w-full px-3 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium">
                    <option value="All" ${data.sex_applicable === 'All' ? 'selected' : ''}>Tous</option>
                    <option value="M" ${data.sex_applicable === 'M' ? 'selected' : ''}>Homme</option>
                    <option value="F" ${data.sex_applicable === 'F' ? 'selected' : ''}>Femme</option>
                </select>
            </div>

            <!-- Age Min -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Âge Min</label>
                <div class="bg-blue-50 rounded-lg p-1.5 mb-2 min-h-[32px] flex items-center justify-center">
                    <p class="text-[11px] font-semibold text-blue-700 text-center age-min-display leading-tight">
                        ${formatAgeDisplay(data.age_min_years, data.age_min_months, data.age_min_days)}
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="relative">
                        <input type="number" name="normal_ranges[${index}][age_min_years]" value="${data.age_min_years ?? ''}" 
                              class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                              placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                        <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">ans</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="normal_ranges[${index}][age_min_months]" value="${data.age_min_months ?? ''}" 
                              class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                              placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                        <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">mois</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="normal_ranges[${index}][age_min_days]" value="${data.age_min_days ?? ''}" 
                              class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                              placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                        <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">j</span>
                    </div>
                </div>
            </div>

            <!-- Age Max -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Âge Max</label>
                <div class="bg-blue-50 rounded-lg p-1.5 mb-2 min-h-[32px] flex items-center justify-center">
                    <p class="text-[11px] font-semibold text-blue-700 text-center age-max-display leading-tight">
                        ${formatAgeDisplay(data.age_max_years, data.age_max_months, data.age_max_days)}
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="relative">
                        <input type="number" name="normal_ranges[${index}][age_max_years]" value="${data.age_max_years ?? ''}" 
                              class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                              placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                        <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">ans</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="normal_ranges[${index}][age_max_months]" value="${data.age_max_months ?? ''}" 
                              class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                              placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                        <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">mois</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="normal_ranges[${index}][age_max_days]" value="${data.age_max_days ?? ''}" 
                              class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                              placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                        <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">j</span>
                    </div>
                </div>
            </div>

            <!-- Normal Min -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Min</label>
                <div class="relative">
                    <input type="number" step="0.01" name="normal_ranges[${index}][normal_min]" value="${data.normal_min ?? ''}" 
                          class="w-full px-3 pr-14 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                          placeholder="0.00">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded unit-display pointer-events-none">
                        ${currentUnit || ''}
                    </span>
                </div>
            </div>

            <!-- Normal Max -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Max</label>
                <div class="relative">
                    <input type="number" step="0.01" name="normal_ranges[${index}][normal_max]" value="${data.normal_max ?? ''}" 
                          class="w-full px-3 pr-14 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                          placeholder="0.00">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded unit-display pointer-events-none">
                        ${currentUnit || ''}
                    </span>
                </div>
            </div>
        </div>

        <!-- Second Row: Pregnancy Checkbox + Delete -->
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
            <div class="pregnancy-wrapper ${data.sex_applicable === 'F' ? '' : 'hidden'}">
                <label class="flex items-center gap-2 px-3 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-pink-50 hover:border-pink-300 transition-all">
                    <input type="checkbox" name="normal_ranges[${index}][pregnant_applicable]" value="1" ${data.pregnant_applicable ? 'checked' : ''}
                        class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                    <span class="text-sm font-medium text-gray-700">🤰 Grossesse</span>
                </label>
            </div>

            <!-- Delete Button -->
            <button type="button" onclick="removeNormalRange(this)"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 text-sm font-medium border border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Supprimer
            </button>
        </div>
    `;

    container.appendChild(wrapper);
    attachSexChangeListener(wrapper);
    updateNoRangesMessage();
    updateRangeNumbers();

    // Smooth scroll + animation
    setTimeout(() => {
        const offset = 100;
        const elementPosition = wrapper.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - offset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });

        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(15px)';
        wrapper.classList.add('ring-2', 'ring-[#bc1622]/30');

        requestAnimationFrame(() => {
            wrapper.style.transition = 'all 400ms cubic-bezier(.16,.84,.44,1)';
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });

        const focusTarget = wrapper.querySelector(`input[name="normal_ranges[${index}][age_min_years]"]`);

        setTimeout(() => {
            focusTarget?.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            setTimeout(() => {
                focusTarget?.focus({ preventScroll: true });
            }, 350);
        }, 450);

        setTimeout(() => {
            wrapper.classList.remove('ring-2', 'ring-[#bc1622]/30');
        }, 1500);
    }, 30);
}

function formatAgeDisplay(years, months, days) {
    years = parseInt(years) || 0;
    months = parseInt(months) || 0;
    days = parseInt(days) || 0;
    
    if (!years && !months && !days) return '∞';
    
    let parts = [];
    if (years) parts.push(years + (years > 1 ? ' ans' : ' an'));
    if (months) parts.push(months + ' mois');
    if (days) parts.push(days + ' j');  // ✅ ALWAYS show days if present
    
    return parts.join(' ') || '∞';
}
function normalizeAge(years, months, days) {
    years = parseInt(years) || 0;
    months = parseInt(months) || 0;
    days = parseInt(days) || 0;
    
    // Convert days overflow to months
    if (days >= 30) {
        const extraMonths = Math.floor(days / 30);
        months += extraMonths;
        days = days % 30;
    }
    
    // Convert months overflow to years
    if (months >= 12) {
        const extraYears = Math.floor(months / 12);
        years += extraYears;
        months = months % 12;
    }
    
    return { years, months, days };
}

function updateAgeDisplayForRange(input) {
    const wrapper = input.closest('.normal-range-item');
    const isMin = input.name.includes('age_min');
    
    const yearInput = wrapper.querySelector(`input[name*="age_${isMin ? 'min' : 'max'}_years"]`);
    const monthInput = wrapper.querySelector(`input[name*="age_${isMin ? 'min' : 'max'}_months"]`);
    const dayInput = wrapper.querySelector(`input[name*="age_${isMin ? 'min' : 'max'}_days"]`);
    
    let years = parseInt(yearInput?.value) || 0;
    let months = parseInt(monthInput?.value) || 0;
    let days = parseInt(dayInput?.value) || 0;
    
    // ✅ Auto-normalize overflow
    const normalized = normalizeAge(years, months, days);
    
    // ✅ Update input fields with normalized values
    if (yearInput) yearInput.value = normalized.years || '';
    if (monthInput) monthInput.value = normalized.months || '';
    if (dayInput) dayInput.value = normalized.days || '';
    
    // ✅ Update display
    const displayElement = wrapper.querySelector(`.age-${isMin ? 'min' : 'max'}-display`);
    if (displayElement) {
        displayElement.textContent = formatAgeDisplay(normalized.years, normalized.months, normalized.days);
    }
}
// Initialize currentUnit on page load
let currentUnit = '';
const unitSelect = document.getElementById('unit_select');
if (unitSelect) {
    currentUnit = unitSelect.options[unitSelect.selectedIndex]?.text || '';
    unitSelect.addEventListener('change', function() {
        currentUnit = this.options[this.selectedIndex].text;
        document.querySelectorAll('.unit-display').forEach(span => {
            span.textContent = currentUnit;
        });
    });
}
function updateRangeNumbers() {
    const ranges = document.querySelectorAll('.normal-range-item');
    ranges.forEach((range, index) => {
        const numberBadge = range.querySelector('.range-number');
        if (numberBadge) {
            numberBadge.textContent = `#${index + 1}`;
        }
    });
}
// ✅ Remove Normal Range
function removeNormalRange(button) {
  const wrapper = button.closest('.normal-range-item');
    if (confirm('Remove this normal range?')) {
        wrapper.style.transition = 'all 300ms ease-out';
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            wrapper.remove();
            updateNoRangesMessage();
            updateRangeNumbers(); // ✅ Re-number after deletion
        }, 300);
    }
   
}

// ✅ Attach Sex Change Listener
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

// ✅ Update No Ranges Message
function updateNoRangesMessage() {
    const container = document.getElementById('normalRangesContainer');
    const noRangesMsg = document.getElementById('noRangesMessage');
    
    if (!container || !noRangesMsg) return;
    
    if (container.children.length === 0) {
        noRangesMsg.classList.remove('hidden');
    } else {
        noRangesMsg.classList.add('hidden');
    }
}

// ✅ Load Compatible Devices
async function loadCompatibleDevices() {
    const container = document.getElementById('deviceSelectionContainer');
    const summary = document.getElementById('deviceSelectionSummary');
    
    if (!container) return;
    
    try {
        const res = await fetch("{{ route('lab-devices.index') }}", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!res.ok) throw new Error('Device list fetch failed');
        
        const text = await res.text();
        let devices;
        
        try {
            devices = JSON.parse(text);
        } catch {
            console.error('Received HTML instead of JSON');
            throw new Error('Invalid response format');
        }

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

        const devicesByType = devices.reduce((acc, device) => {
            const type = device.device_type || 'Other';
            if (!acc[type]) acc[type] = [];
            acc[type].push(device);
            return acc;
        }, {});

        Object.entries(devicesByType).forEach(([type, typeDevices]) => {
            const typeSection = document.createElement('div');
            typeSection.className = 'space-y-2 p-4';
            
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
                                   class="device-checkbox mt-0.5 h-4 w-4 text-amber-600 border-gray-300 rounded">
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

        if (summary) summary.classList.remove('hidden');
        attachDeviceListeners();
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

// ✅ Update Device Count
function updateDeviceCount() {
    const checked = document.querySelectorAll('input[name="device_ids[]"]:checked');
    const countSpan = document.getElementById('selectedDeviceCount');
    if (countSpan) {
        countSpan.textContent = checked.length;
    }
}

// ✅ Attach Device Listeners
function attachDeviceListeners() {
    const checkboxes = document.querySelectorAll('.device-checkbox');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeviceCount);
    });
}

// ✅ Modal Functions
function openCategoryModal() { 
    const modal = document.getElementById('categoryModal');
    if (modal) modal.classList.remove('hidden'); 
}

function closeCategoryModal() { 
    const modal = document.getElementById('categoryModal');
    if (modal) {
        modal.classList.add('hidden');
        const nameInput = document.getElementById('categoryName');
        const errorDiv = document.getElementById('categoryError');
        if (nameInput) nameInput.value = '';
        if (errorDiv) errorDiv.textContent = '';
    }
}

function openSampleTypeModal() { 
    const modal = document.getElementById('sampleTypeModal');
    if (modal) modal.classList.remove('hidden'); 
}

function closeSampleTypeModal() { 
    const modal = document.getElementById('sampleTypeModal');
    if (modal) {
        modal.classList.add('hidden');
        const nameInput = document.getElementById('sampleTypeName');
        const errorDiv = document.getElementById('sampleTypeError');
        if (nameInput) nameInput.value = '';
        if (errorDiv) errorDiv.textContent = '';
    }
}

function openUnitModal() { 
    const modal = document.getElementById('unitModal');
    if (modal) modal.classList.remove('hidden'); 
}

function closeUnitModal() { 
    const modal = document.getElementById('unitModal');
    if (modal) {
        modal.classList.add('hidden');
        const nameInput = document.getElementById('unitName');
        const errorDiv = document.getElementById('unitError');
        if (nameInput) nameInput.value = '';
        if (errorDiv) errorDiv.textContent = '';
    }
}

// ✅ DOMContentLoaded - Event listeners only
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('normalRangesContainer');
    if (container) {
        rangeCounter = container.querySelectorAll('.normal-range-item').length;
        updateNoRangesMessage();
        
        container.querySelectorAll('.normal-range-item').forEach(item => {
            attachSexChangeListener(item);
        });
    }

    loadCompatibleDevices();
    
    const formulaInput = document.getElementById('formula');
    if (formulaInput) {
        formulaInput.addEventListener('input', updateFormulaPreview);
    }
    
    loadFormulas();
    
    // Category Form
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const nameInput = document.getElementById('categoryName');
            const errorDiv = document.getElementById('categoryError');
            
            if (!nameInput || !errorDiv) return;
            
            const name = nameInput.value.trim();
            
            if (!name) {
                errorDiv.textContent = 'Category name is required';
                return;
            }
            
            const select = document.querySelector('select[name="category_analyse_id"]');
            if (!select) {
                console.error('Category select not found');
                return;
            }
            
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
    }
    
    // Sample Type Form
const sampleTypeForm = document.getElementById('sampleTypeForm');
if (sampleTypeForm) {
    sampleTypeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const nameInput = document.getElementById('sampleTypeName');
        const errorDiv = document.getElementById('sampleTypeError');
        
        if (!nameInput || !errorDiv) return;
        
        const name = nameInput.value.trim();
        if (!name) {
            errorDiv.textContent = 'Sample type name is required';
            return;
        }
        
        // ✅ CONTEXT-AWARE SELECT FINDER
        let select = document.getElementById('edit-sample-type-select') ||  // Edit view
                    document.getElementById('sampletypeids') ||           // Create view
                    document.querySelector('select[name="sampletypeids"]'); // Fallback
        
        if (!select) {
            console.error('Sample type select not found - checked IDs: edit-sample-type-select, sampletypeids');
            errorDiv.textContent = 'Erreur: Sélecteur non trouvé';
            return;
        }
        
        // Check for duplicates (case-insensitive)
        const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());
        if (existingOptions.includes(name.toLowerCase())) {
            errorDiv.textContent = 'Sample type already exists';
            return;
        }
        
        // Generate unique temp ID
        if (typeof tempIdCounter === 'undefined') tempIdCounter = -1;
        const tempId = 'temp_' + (tempIdCounter--);
        
        // Global temp tracking
        if (typeof tempSampleTypes === 'undefined') window.tempSampleTypes = [];
        const tempSampleType = { id: tempId, name: name, isTemp: true };
        window.tempSampleTypes.push(tempSampleType);
        
        // Add styled option
        const option = new Option(name + ' (New)', tempId, true, true);
        option.className = 'text-emerald-600 font-medium bg-emerald-50';
        select.add(option);
        
        // Trigger chip addition (if chips system exists)
        if (typeof addChip === 'function') {
            addChip(name + ' (New)', tempId);
        }
        
        // Clear & close
        nameInput.value = '';
        errorDiv.textContent = '';
        closeSampleTypeModal();
        showToast(`"${name}" ajouté temporairement`, 'success');
    });
}

    
    // Unit Form
    const unitForm = document.getElementById('unitForm');
    if (unitForm) {
        unitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const nameInput = document.getElementById('unitName');
            const errorDiv = document.getElementById('unitError');
            
            if (!nameInput || !errorDiv) return;
            
            const name = nameInput.value.trim();
            
            if (!name) {
                errorDiv.textContent = 'Unit name is required';
                return;
            }
            
            const select = document.querySelector('select[name="unit_id"]');
            if (!select) {
                console.error('Unit select not found');
                return;
            }
            
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
    }
    
    // Main form submission
    const mainForm = document.getElementById('analysisCreateForm');
    if (mainForm) {
        mainForm.addEventListener('submit', async function (e) {
            e.preventDefault();

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

            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            try {
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(this),
                });

                if (res.status === 422) {
                    const data = await res.json();
                    const firstKey = Object.keys(data.errors || {})[0];
                    const firstMsg = firstKey ? data.errors[firstKey][0] : 'Validation error';
                    showToast(`⚠️ ${firstMsg}`, "warning");
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                }

                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    showToast(`❌ ${data.message || 'Failed to save.'}`, "error");
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                }

                const data = await res.json();
                showToast(`✅ ${data.message || 'Saved successfully.'}`, "success");

                setTimeout(() => {
                    window.location.href = data.redirect || "{{ route('analyses.index') }}";
                }, 700);

            } catch (err) {
                console.error(err);
                showToast("❌ Network error. Please try again.", "error");
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }
    
    // Close modals on outside click
    document.addEventListener('click', function(e) {
        if (e.target.id === 'categoryModal') closeCategoryModal();
        if (e.target.id === 'sampleTypeModal') closeSampleTypeModal();
        if (e.target.id === 'unitModal') closeUnitModal();
    });
});
</script>


@endsection
