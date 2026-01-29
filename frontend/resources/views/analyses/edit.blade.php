{{-- resources/views/analyses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Analysis')

@section('content')

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast-enter {
    animation: slideInRight 0.3s ease-out forwards;
}

.toast-exit {
    animation: slideOutRight 0.3s ease-in forwards;
}
</style>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
   
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Analysis</h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Update analysis information, pricing, and normal ranges
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

        <form action="{{ route('analyses.update', $analysis['id']) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Information Card --}}
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
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Analysis Code
                            </label>
                            <input type="text" 
                                   name="code" 
                                   value="{{ old('code', $analysis['code']) }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white"
                                   placeholder="e.g., CBC-001">
                            @error('code')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Analysis Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $analysis['name']) }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
                                   placeholder="e.g., Complete Blood Count"
                                   required>
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select name="category_analyse_id" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}"
                                        {{ old('category_analyse_id', $analysis['category_analyse']['id'] ?? null) == $category['id'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_analyse_id')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">DZD</span>
                                <input type="number" 
                                       name="price" 
                                       value="{{ old('price', $analysis['price'] ?? '') }}" 
                                       step="0.01" 
                                       min="0" 
                                       class="w-full pl-16 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('price')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                            <select name="unit_id" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit['id'] }}"
                                        {{ old('unit_id', $analysis['unit']['id'] ?? null) == $unit['id'] ? 'selected' : '' }}>
                                        {{ $unit['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

           {{-- Specifications Card --}}
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Specifications
                    </h2>
                </div>
                    <div class="p-6">
                        <div class="max-w-md">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Types d'Échantillon <span class="text-red-500">*</span>
                            </label>

                            <div class="flex gap-2 relative">
                                <!-- Chips container -->
                                <div id="edit-chips-container" 
                                    class="flex-1 flex flex-wrap items-center gap-1.5 px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 hover:bg-white focus-within:ring-2 focus-within:ring-purple-500 transition-all duration-200 cursor-text min-h-[48px]">

                                    <div id="edit-selected-chips" class="flex flex-wrap gap-1.5"></div>
                                    <span id="edit-placeholder" class="text-gray-400 select-none">Sélectionnez les types d'échantillon...</span>

                                    <!-- Hidden select -->
                                    <select name="sample_type_ids[]" 
                                            id="edit-sample-type-select"
                                            multiple 
                                            class="hidden">
                                        @foreach($sampleTypes as $type)
                                            <option value="{{ $type['id'] }}"
                                                {{ in_array($type['id'], old('sample_type_ids', $analysis['sample_type_ids'] ?? [])) ? 'selected' : '' }}>
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
       {{-- Normal Ranges Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Plages normales
        </h2>
    </div>

    <div class="p-6">
        <div id="normalRangesContainer" class="space-y-4">
            @foreach($analysis['normal_ranges'] ?? [] as $index => $range)
            @php
                // Convert days back to years/months/days
                $ageMinDays = $range['age_min'] ?? 0;
                $ageMaxDays = $range['age_max'] ?? null;
                
                // Min age conversion
                $minYears = floor($ageMinDays / 365);
                $minMonths = floor(($ageMinDays % 365) / 30);
                $minDays = $ageMinDays % 30;
                
                // Max age conversion
                if ($ageMaxDays) {
                    $maxYears = floor($ageMaxDays / 365);
                    $maxMonths = floor(($ageMaxDays % 365) / 30);
                    $maxDays = $ageMaxDays % 30;
                } else {
                    $maxYears = null;
                    $maxMonths = null;
                    $maxDays = null;
                }
            @endphp
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-2 border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200" data-range-index="{{ $index }}">
                
                <!-- Range Counter Header -->
                <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-gray-300">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-sm rounded-full shadow-md range-number">
                            #{{ $index + 1 }}
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
                        <select name="normal_ranges[{{ $index }}][sex_applicable]" class="sex-select w-full px-3 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium">
                            <option value="All" {{ ($range['sex_applicable'] ?? 'All') == 'All' ? 'selected' : '' }}>Tous</option>
                            <option value="M" {{ ($range['sex_applicable'] ?? '') == 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ ($range['sex_applicable'] ?? '') == 'F' ? 'selected' : '' }}>Femme</option>
                        </select>
                    </div>

                    <!-- Age Min -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Âge Min</label>
                        <div class="bg-blue-50 rounded-lg p-1.5 mb-2 min-h-[32px] flex items-center justify-center">
                            <p class="text-[11px] font-semibold text-blue-700 text-center age-min-display leading-tight">
                                {{ formatAgeDisplay($minYears, $minMonths, $minDays) }}
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-1.5">
                            <div class="relative">
                                <input type="number" name="normal_ranges[{{ $index }}][age_min_years]" value="{{ old("normal_ranges.$index.age_min_years", $minYears ?: '') }}" 
                                      class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                                      placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                                <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">ans</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="normal_ranges[{{ $index }}][age_min_months]" value="{{ old("normal_ranges.$index.age_min_months", $minMonths ?: '') }}" 
                                      class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                                      placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                                <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">mois</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="normal_ranges[{{ $index }}][age_min_days]" value="{{ old("normal_ranges.$index.age_min_days", $minDays ?: '') }}" 
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
                                {{ formatAgeDisplay($maxYears, $maxMonths, $maxDays) }}
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-1.5">
                            <div class="relative">
                                <input type="number" name="normal_ranges[{{ $index }}][age_max_years]" value="{{ old("normal_ranges.$index.age_max_years", $maxYears ?: '') }}" 
                                      class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                                      placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                                <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">ans</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="normal_ranges[{{ $index }}][age_max_months]" value="{{ old("normal_ranges.$index.age_max_months", $maxMonths ?: '') }}" 
                                      class="w-full px-1.5 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium age-input"
                                      placeholder="0" min="0" onchange="updateAgeDisplayForRange(this)">
                                <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] text-gray-500 font-medium whitespace-nowrap">mois</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="normal_ranges[{{ $index }}][age_max_days]" value="{{ old("normal_ranges.$index.age_max_days", $maxDays ?: '') }}" 
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
                            <input type="number" step="0.01" name="normal_ranges[{{ $index }}][normal_min]" value="{{ old("normal_ranges.$index.normal_min", $range['normal_min'] ?? '') }}" 
                                  class="w-full px-3 pr-14 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                                  placeholder="0.00">
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded unit-display pointer-events-none">
                                {{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}
                            </span>
                        </div>
                    </div>

                    <!-- Normal Max -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Max</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="normal_ranges[{{ $index }}][normal_max]" value="{{ old("normal_ranges.$index.normal_max", $range['normal_max'] ?? '') }}" 
                                  class="w-full px-3 pr-14 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                                  placeholder="0.00">
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded unit-display pointer-events-none">
                                {{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Second Row: Pregnancy Checkbox + Delete -->
                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="pregnancy-wrapper {{ ($range['sex_applicable'] ?? 'All') === 'F' ? '' : 'hidden' }}">
                        <label class="flex items-center gap-2 px-3 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-pink-50 hover:border-pink-300 transition-all">
                            <input type="checkbox" name="normal_ranges[{{ $index }}][pregnant_applicable]" value="1" {{ old("normal_ranges.$index.pregnant_applicable", $range['pregnant_applicable'] ?? false) ? 'checked' : '' }}
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
            </div>
            @endforeach
        </div>
        
        <button type="button" 
                onclick="addNormalRange()" 
                class="mt-6 inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 hover:shadow-lg transition-all duration-200 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Ajouter une plage normale
        </button>
    </div>
</div>

{{-- Add formatAgeDisplay helper function at the top of your blade file --}}
@php
function formatAgeDisplay($years, $months, $days) {
    if (!$years && !$months && !$days) return '∞';
    
    $parts = [];
    if ($years) $parts[] = $years . ' an' . ($years > 1 ? 's' : '');
    if ($months) $parts[] = $months . ' mois';
    if ($days) $parts[] = $days . ' j';
    
    return implode(' ', $parts) ?: '0';
}
@endphp
      
{{-- Formula Card - MATCHING YOUR DESIGN SYSTEM --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
    
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Formule de calcul
            </h2>
            <button type="button" onclick="openSavedFormulas()"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 text-xs font-medium border border-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Formules enregistrées
            </button>
        </div>
    </div>
    
    {{-- Main Content --}}
    <div class="p-6 space-y-5">
        
        {{-- Formula Name --}}
        <div>
            <label for="formula_name" class="block text-sm font-semibold text-gray-700 mb-2">
                Nom de la formule
            </label>
            <input type="text" id="formula_name" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
                placeholder="Ex : Formule de Friedewald"
                value="{{ old('formula_name', '') }}">
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            
            {{-- LEFT: Editor Section --}}
            <div class="space-y-4">
                
                {{-- Formula Editor --}}
                <div>
                    <label for="formula" class="block text-sm font-semibold text-gray-700 mb-2">
                        Expression mathématique
                    </label>
                    <textarea id="formula" 
                              name="formula"
                              rows="6" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white font-mono text-sm resize-none"
                              placeholder="Exemple : LDL-C = CT - HDL-C - TG / 5">{{ old('formula', $analysis['formula']) }}</textarea>
                    @error('formula')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Preview --}}
                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                    <div class="flex items-start gap-2 mb-2">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Aperçu</span>
                    </div>
                    <span id="formulaDisplay" class="text-sm font-mono text-gray-800 break-all block">
                        {{ $analysis['formula'] ?? 'Aucune formule définie' }}
                    </span>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button type="button" onclick="clearFormula()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 hover:border-red-400 hover:shadow-md transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Effacer
                    </button>
                    <button id="saveFormulaBtn" type="button" onclick="saveFormula()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 hover:shadow-lg transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </div>

            {{-- RIGHT: Tools Section --}}
            <div class="space-y-5">
                
                {{-- Operators --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Opérateurs et fonctions
                    </label>
                    
                    {{-- Basic Operators --}}
                    <div class="mb-3">
                        <p class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Basiques</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $basicOps = [
                                    ['+', 'Addition'],
                                    ['-', 'Soustraction'],
                                    ['*', 'Multiplication'],
                                    ['/', 'Division'],
                                    ['=', 'Égal'],
                                    ['(', 'Parenthèse ('],
                                    [')', 'Parenthèse )'],
                                    ['^', 'Puissance']
                                ];
                            @endphp
                            @foreach($basicOps as [$symbol, $desc])
                                <button type="button" 
                                    class="inline-flex items-center justify-center min-w-[2.25rem] px-3 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 hover:border-indigo-300 text-indigo-700 text-sm font-semibold rounded-lg transition-all duration-200 hover:shadow-md"
                                    onclick="insertFormula('{{ $symbol }}')"
                                    title="{{ $desc }}">
                                    {{ $symbol }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Advanced Functions --}}
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Fonctions avancées</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $advOps = [
                                    ['√', 'Racine carrée'],
                                    ['ln()', 'Logarithme naturel'],
                                    ['exp()', 'Exponentielle'],
                                    ['mean()', 'Moyenne'],
                                    ['sd()', 'Écart-type'],
                                    ['zscore()', 'Score Z'],
                                    ['min()', 'Minimum'],
                                    ['max()', 'Maximum']
                                ];
                            @endphp
                            @foreach($advOps as [$symbol, $desc])
                                <button type="button" 
                                    class="inline-flex items-center px-3 py-1.5 bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 text-purple-700 text-xs font-semibold rounded-lg transition-all duration-200 hover:shadow-md"
                                    onclick="insertFormula('{{ $symbol }}')"
                                    title="{{ $desc }}">
                                    {{ $symbol }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Search Analyses --}}
                <div>
                    <label for="analysisSearch" class="block text-sm font-semibold text-gray-700 mb-2">
                        Rechercher une analyse
                    </label>
                    <div class="relative">
                        <input type="text" id="analysisSearch" 
                            placeholder="Code ou nom..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white"
                            oninput="filterAnalyses()" />
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Analysis Codes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Codes d'analyses
                    </label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto">
                        <div id="analysisButtons" class="flex flex-wrap gap-2">
                            @if(isset($analyses) && count($analyses) > 0)
                                @foreach($analyses as $analysisItem)
                                    <button type="button" 
                                        class="inline-flex items-center px-3 py-1.5 bg-white hover:bg-blue-50 text-xs font-medium border border-gray-300 hover:border-blue-300 text-gray-700 hover:text-blue-700 rounded-lg transition-all duration-200 analysis-btn"
                                        onclick="insertFormula('{{ $analysisItem['code'] }}')"
                                        data-code="{{ $analysisItem['code'] }}"
                                        data-name="{{ $analysisItem['name'] ?? 'Analyse' }}"
                                        title="{{ $analysisItem['name'] ?? 'Analyse' }}">
                                        {{ $analysisItem['code'] }}
                                    </button>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center w-full py-4 text-center">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">Aucune analyse disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Saved Formulas Modal --}}
    <div id="savedFormulasModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg border border-gray-200 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h4 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Formules enregistrées
                </h4>
                <button onclick="closeSavedFormulas()" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="savedFormulasList" class="p-6 space-y-2 max-h-96 overflow-y-auto"></div>
        </div>
    </div>
</div>


            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-indigo-50 hover:border-indigo-200 transition-all">
                        <input type="checkbox" 
                            name="is_active" 
                            value="1" 
                            id="is_active"
                            {{ old('is_active', $analysis['is_active'] ?? true) ? 'checked' : '' }}
                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition">
                        <label for="is_active" class="text-sm font-medium text-gray-900 cursor-pointer select-none">
                            Analysis is active and available for use
                        </label>
                    </div>
                    
                    {{-- ✅ Add visual indicator --}}
                    <p class="mt-2 text-xs text-gray-500">
                        <span id="status-indicator">
                            Status actuel: 
                            <strong class="{{ ($analysis['is_active'] ?? true) ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($analysis['is_active'] ?? true) ? 'Actif' : 'Inactif' }}
                            </strong>
                        </span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
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
                    Update Analysis
                </button>

            </div>
        </form>
    </div>
</div>

{{-- PHP Helper Function --}}
@php
function formatAgeClinical($days) {
    if ($days === null) return '∞';

    if ($days <= 28) {
        $label = 'Nouveau-né';
    } elseif ($days <= 365) {
        $label = 'Nourrisson';
    } elseif ($days <= 365 * 3) {
        $label = 'Petit enfant';
    } elseif ($days <= 365 * 12) {
        $label = 'Enfant';
    } elseif ($days <= 365 * 18) {
        $label = 'Adolescent';
    } else {
        $label = 'Adulte';
    }

    $years = floor($days / 365);
    $months = floor(($days % 365) / 30);
    $daysRemain = $days % 30;

    $parts = [];
    if ($years > 0) $parts[] = $years . ' an' . ($years > 1 ? 's' : '');
    if ($months > 0) $parts[] = $months . ' mois';
    if ($daysRemain > 0 && $years == 0) $parts[] = $daysRemain . ' j';

    $exact = implode(' ', $parts);
    return "$label ($exact)";
}
@endphp
{{-- Modals --}}
@include('analyses.partials.category-modal')
@include('analyses.partials.sample-type-modal')
@include('analyses.partials.unit-modal')
{{-- Toast Notification Container --}}
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
<style>
/* Chips styling */
#edit-selected-chips > div {
    @apply inline-flex items-center gap-2 px-3 py-1.5 bg-purple-50 text-purple-700 text-sm font-medium rounded-full border border-purple-200 shadow-sm transition-all duration-200;
}
#edit-selected-chips > div:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(128, 90, 213, 0.25);
}
#edit-selected-chips > div button {
    @apply w-6 h-6 flex items-center justify-center rounded-full text-sm text-purple-700 bg-purple-100 hover:bg-red-500 hover:text-white transition-all;
}
@keyframes slideOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(-10px); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('edit-sample-type-select');
    const chipsContainer = document.getElementById('edit-selected-chips');
    const placeholder = document.getElementById('edit-placeholder');
    const container = document.getElementById('edit-chips-container');

    const updatePlaceholder = () => placeholder.style.display = chipsContainer.children.length ? 'none' : 'block';

    const addChip = (text, value) => {
        if ([...chipsContainer.children].some(chip => chip.dataset.value === value)) return;

        const chip = document.createElement('div');
        chip.className = 'inline-flex items-center gap-2 px-3 py-1.5 bg-purple-50 text-purple-700 text-sm font-medium rounded-full border border-purple-200 shadow-sm transition-all duration-200';
        chip.dataset.value = value;
        chip.innerHTML = `<span>${text}</span><button type="button">&times;</button>`;

        // Make X button easy to click
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

    // Initialize chips with existing selected values (for edit)
    Array.from(select.selectedOptions).forEach(option => addChip(option.textContent.trim(), option.value));

    // Simple dropdown
    container.addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON') return;

        let dropdown = document.createElement('div');
        dropdown.id = 'edit-multi-select-dropdown';
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
        const oldDropdown = document.getElementById('edit-multi-select-dropdown');
        if (oldDropdown) oldDropdown.remove();
        container.appendChild(dropdown);
    });

    // Close dropdown on outside click
    document.addEventListener('click', e => {
        if (!container.contains(e.target)) {
            const dropdown = document.getElementById('edit-multi-select-dropdown');
            if (dropdown) dropdown.remove();
        }
    });

    updatePlaceholder();
});
</script>
{{-- JavaScript --}}
<script>
let rangeCounter = {{ count($analysis['normal_ranges'] ?? []) }};
let currentUnit = '{{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}';

// ============================================
// TOAST NOTIFICATION FUNCTIONS
// ============================================

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    toast.id = toastId;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 border-green-600',
        error: 'bg-red-500 border-red-600',
        warning: 'bg-orange-500 border-orange-600',
        info: 'bg-blue-500 border-blue-600'
    };
    
    const icons = {
        success: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>`,
        error: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`,
        warning: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>`,
        info: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
               </svg>`
    };
    
    toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg border-l-4 flex items-center gap-3 min-w-[320px] max-w-md toast-enter`;
    toast.innerHTML = `
        <div class="flex-shrink-0">
            ${icons[type]}
        </div>
        <div class="flex-1">
            <p class="font-medium text-sm">${message}</p>
        </div>
        <button onclick="closeToast('${toastId}')" class="flex-shrink-0 hover:bg-white/20 rounded p-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        closeToast(toastId);
    }, 5000);
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (!toast) return;
    
    toast.classList.remove('toast-enter');
    toast.classList.add('toast-exit');
    
    setTimeout(() => {
        toast.remove();
    }, 300);
}

// ============================================
// UNIT UPDATE
// ============================================

document.querySelector('select[name="unit_id"]')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const unitText = selectedOption.text;
    currentUnit = unitText;
    
    document.querySelectorAll('.unit-display').forEach(span => {
        span.textContent = unitText;
    });
});
// ============================================
// STATUS INDICATOR UPDATE
// ============================================
document.getElementById('is_active')?.addEventListener('change', function() {
    const indicator = document.getElementById('status-indicator');
    if (indicator) {
        if (this.checked) {
            indicator.innerHTML = 'Status actuel: <strong class="text-green-600">Actif ✓</strong> <span class="text-xs text-orange-500">(non enregistré)</span>';
        } else {
            indicator.innerHTML = 'Status actuel: <strong class="text-red-600">Inactif ✗</strong> <span class="text-xs text-orange-500">(non enregistré)</span>';
        }
    }
});

// ============================================
// FORM SUBMISSION WITH SPINNER
// ============================================

document.querySelector('form')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Enregistrement en cours...
        `;
    }
    
    // Show loading overlay
    showLoadingOverlay();
});

function showLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
    overlay.innerHTML = `
        <div class="bg-white rounded-lg p-8 shadow-2xl flex flex-col items-center gap-4">
            <svg class="animate-spin h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-700 font-medium text-lg">Enregistrement en cours...</p>
            <p class="text-gray-500 text-sm">Veuillez patienter</p>
        </div>
    `;
    document.body.appendChild(overlay);
}
// ============================================
// SAMPLE TYPE MODAL FUNCTIONS
// ============================================
let tempSampleTypes = []; // Track temporary types

function saveTempSampleType() {
  const nameInput = document.getElementById('sampleTypeName');
  const name = nameInput.value.trim();
  if (!name) {
    document.getElementById('sampleTypeError').textContent = 'Nom requis';
    return;
  }
  
  // Create temp ID (timestamp-based, unique)
  const tempId = 'temp_' + Date.now();
  
  // Add to hidden select as option
  const select = document.getElementById('edit-sample-type-select');
  const option = document.createElement('option');
  option.value = tempId;
  option.textContent = name;
  option.selected = true;
  select.appendChild(option);
  
  // Add chip immediately
  addChip(name, tempId);
  
  // Track for backend
  tempSampleTypes.push({id: tempId, name: name});
  
  closeSampleTypeModal();
  showToast(`"${name}" ajouté temporairement`, 'success');
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

// ============================================
// FORMULA BUILDER FUNCTIONS
// ============================================

function insertFormula(text) {
    const textarea = document.getElementById('formula');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const currentValue = textarea.value;
    
    textarea.value = currentValue.substring(0, start) + text + currentValue.substring(end);
    
    const newPos = start + text.length;
    textarea.setSelectionRange(newPos, newPos);
    textarea.focus();
    
    updateFormulaPreview();
}

function updateFormulaPreview() {
    const formula = document.getElementById('formula')?.value || '';
    const display = document.getElementById('formulaDisplay');
    if (display) {
        display.textContent = formula || 'Aucune formule';
    }
}

function clearFormula() {
    if (confirm('Êtes-vous sûr de vouloir effacer la formule?')) {
        const formulaInput = document.getElementById('formula');
        const nameInput = document.getElementById('formula_name');
        
        if (formulaInput) formulaInput.value = '';
        if (nameInput) nameInput.value = '';
        
        updateFormulaPreview();
        showToast('Formule effacée', 'info');
    }
}

function filterAnalyses() {
    const searchTerm = document.getElementById('analysisSearch')?.value.toLowerCase() || '';
    const buttons = document.querySelectorAll('.analysis-btn');
    
    buttons.forEach(btn => {
        const code = (btn.dataset.code || '').toLowerCase();
        const name = (btn.dataset.name || '').toLowerCase();
        
        btn.style.display = (code.includes(searchTerm) || name.includes(searchTerm)) ? 'inline-block' : 'none';
    });
}

function saveFormula() {
    const formula = document.getElementById('formula')?.value;
    const nameInput = document.getElementById('formula_name');
    const name = nameInput?.value;
    
    if (!formula) {
        showToast('Veuillez entrer une formule avant de sauvegarder', 'warning');
        return;
    }
    
    if (!name) {
        showToast('Veuillez donner un nom à la formule', 'warning');
        return;
    }
    
    let formulas = JSON.parse(localStorage.getItem('saved_formulas') || '[]');
    
    formulas.push({
        name: name,
        formula: formula,
        date: new Date().toISOString()
    });
    
    localStorage.setItem('saved_formulas', JSON.stringify(formulas));
    showToast('✅ Formule sauvegardée avec succès!', 'success');
}

function openSavedFormulas() {
    const modal = document.getElementById('savedFormulasModal');
    const list = document.getElementById('savedFormulasList');
    
    if (!modal || !list) return;
    
    const formulas = JSON.parse(localStorage.getItem('saved_formulas') || '[]');
    
    if (formulas.length === 0) {
        list.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Aucune formule sauvegardée</p>';
    } else {
        list.innerHTML = formulas.map((f, index) => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                <div class="flex-1">
                    <p class="font-medium text-sm text-gray-800">${f.name}</p>
                    <p class="text-xs text-gray-600 font-mono mt-1">${f.formula}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="loadFormula(${index})" 
                        class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
                        Charger
                    </button>
                    <button onclick="deleteFormula(${index})" 
                        class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition">
                        ×
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    modal.classList.remove('hidden');
}

function closeSavedFormulas() {
    document.getElementById('savedFormulasModal')?.classList.add('hidden');
}

function loadFormula(index) {
    const formulas = JSON.parse(localStorage.getItem('saved_formulas') || '[]');
    const formula = formulas[index];
    
    if (formula) {
        const formulaInput = document.getElementById('formula');
        const nameInput = document.getElementById('formula_name');
        
        if (formulaInput) formulaInput.value = formula.formula;
        if (nameInput) nameInput.value = formula.name;
        
        updateFormulaPreview();
        closeSavedFormulas();
        showToast('Formule chargée', 'success');
    }
}

function deleteFormula(index) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette formule?')) {
        let formulas = JSON.parse(localStorage.getItem('saved_formulas') || '[]');
        formulas.splice(index, 1);
        localStorage.setItem('saved_formulas', JSON.stringify(formulas));
        openSavedFormulas();
        showToast('Formule supprimée', 'info');
    }
}

function unlinkSampleType() {
    const select = document.getElementById('sample_type_select');
    if (!select) return;
    
    const currentSelection = select.options[select.selectedIndex].text;
    
    if (select.value === '') {
        showToast('Aucun type d\'échantillon n\'est actuellement associé', 'info');
        return;
    }
    
    if (confirm(`⚠️ Êtes-vous sûr de vouloir dissocier "${currentSelection}"?\n\nLe type d'échantillon restera disponible dans le système.`)) {
        select.value = '';
        showToast(`Type d'échantillon "${currentSelection}" dissocié`, 'success');
    }
}

// ============================================
// AGE DISPLAY FUNCTIONS
// ============================================

function formatAgeClinicalJS(days) {
    if (!days) return '∞';
    
    let label;
    if (days <= 28) label = 'Nouveau-né';
    else if (days <= 365) label = 'Nourrisson';
    else if (days <= 365 * 3) label = 'Petit enfant';
    else if (days <= 365 * 12) label = 'Enfant';
    else if (days <= 365 * 18) label = 'Adolescent';
    else label = 'Adulte';
    
    const years = Math.floor(days / 365);
    const months = Math.floor((days % 365) / 30);
    const daysRemain = days % 30;
    
    let parts = [];
    if (years > 0) parts.push(years + (years > 1 ? ' ans' : ' an'));
    if (months > 0) parts.push(months + ' mois');
    if (daysRemain > 0 && years == 0) parts.push(daysRemain + ' j');
    
    const exact = parts.join(' ');
    return `${label} (${exact})`;
}

function updateAgeDisplayForRange(input) {
    const wrapper = input.closest('[data-range-index]');
    if (!wrapper) return;
    
    const rangeIndex = wrapper.dataset.rangeIndex;
    const isMin = input.name.includes('age_min');
    const displayType = isMin ? 'min' : 'max';
    
    const yearsInput = wrapper.querySelector(`input[name*="age_${displayType}_years"]`);
    const monthsInput = wrapper.querySelector(`input[name*="age_${displayType}_months"]`);
    const daysInput = wrapper.querySelector(`input[name*="age_${displayType}_days"]`);
    
    const years = parseInt(yearsInput?.value) || 0;
    const months = parseInt(monthsInput?.value) || 0;
    const days = parseInt(daysInput?.value) || 0;
    
    const totalDays = (years * 365) + (months * 30) + days;
    
    const display = wrapper.querySelector(`.age-${displayType}-display`);
    if (display) {
        display.textContent = formatAgeDisplay(years, months, days);
    }
}

function formatAgeDisplay(years, months, days) {
    if (!years && !months && !days) return '∞';
    
    let parts = [];
    if (years) parts.push(years + ' an' + (years > 1 ? 's' : ''));
    if (months) parts.push(months + ' mois');
    if (days) parts.push(days + ' j');
    
    return parts.join(' ') || '0';
}

// ============================================
// NORMAL RANGE FUNCTIONS
// ============================================

function addNormalRange() {
    showToast('Nouvelle plage ajoutée', 'info');
    // Your existing addNormalRange code here
}

function removeNormalRange(button) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette plage ?')) {
        button.closest('[data-range-index]').remove();
        showToast('Plage supprimée', 'info');
        updateRangeNumbers();
    }
}

function updateRangeNumbers() {
    document.querySelectorAll('[data-range-index] .range-number').forEach((num, index) => {
        num.textContent = `#${index + 1}`;
    });
}

// ============================================
// FORM SUBMISSION
// ============================================

document.querySelector('form')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Enregistrement...
        `;
        
        showToast('Enregistrement des modifications...', 'info');
    }
});

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize formula preview
    const formulaTextarea = document.getElementById('formula');
    if (formulaTextarea) {
        formulaTextarea.addEventListener('input', updateFormulaPreview);
        updateFormulaPreview();
    }
    
    // Show Laravel flash messages
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
    
    @if(session('warning'))
        showToast("{{ session('warning') }}", 'warning');
    @endif
    
    @if(session('info'))
        showToast("{{ session('info') }}", 'info');
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast("{{ $error }}", 'error');
        @endforeach
    @endif
});
</script>

@endsection
