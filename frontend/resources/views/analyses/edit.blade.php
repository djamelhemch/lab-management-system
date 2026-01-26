{{-- resources/views/analyses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Analysis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sample Type</label>
                        <select name="sample_type_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                            <option value="">Select Sample Type</option>
                            @foreach($sampleTypes as $sampleType)
                                <option value="{{ $sampleType['id'] }}"
                                    {{ old('sample_type_id', $analysis['sample_type']['id'] ?? null) == $sampleType['id'] ? 'selected' : '' }}>
                                    {{ $sampleType['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('sample_type_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Normal Ranges Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Normal Ranges
                    </h2>
                </div>

                <div class="p-6">
                    <div id="normalRangesContainer" class="space-y-4">
                        @foreach($analysis['normal_ranges'] ?? [] as $index => $range)
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-2 border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200">
                            <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 items-end">
                                
                                {{-- Sex --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Sexe</label>
                                    <select name="normal_ranges[{{ $index }}][sex_applicable]" 
                                            class="w-full px-3 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium">
                                        <option value="All" {{ ($range['sex_applicable'] ?? 'All') == 'All' ? 'selected' : '' }}>Tous</option>
                                        <option value="M" {{ ($range['sex_applicable'] ?? '') == 'M' ? 'selected' : '' }}>Homme</option>
                                        <option value="F" {{ ($range['sex_applicable'] ?? '') == 'F' ? 'selected' : '' }}>Femme</option>
                                    </select>
                                </div>

                                {{-- Age Min --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Âge Min</label>
                                    <div class="bg-blue-50 rounded-lg p-2 mb-2 min-h-[44px] flex items-center justify-center">
                                        <p class="text-xs font-semibold text-blue-700 text-center" id="age-min-display-{{ $index }}">
                                            {{ formatAgeClinical($range['age_min']) }}
                                        </p>
                                    </div>
                                    <div class="relative">
                                        <input type="number"
                                            name="normal_ranges[{{ $index }}][age_min]"
                                            value="{{ old("normal_ranges.$index.age_min", $range['age_min']) }}"
                                            class="w-full px-3 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                                            onchange="updateAgeDisplay(this, {{ $index }}, 'min')"
                                            placeholder="jours">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-medium">j</span>
                                    </div>
                                </div>

                                {{-- Age Max --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Âge Max</label>
                                    <div class="bg-blue-50 rounded-lg p-2 mb-2 min-h-[44px] flex items-center justify-center">
                                        <p class="text-xs font-semibold text-blue-700 text-center" id="age-max-display-{{ $index }}">
                                            {{ formatAgeClinical($range['age_max']) }}
                                        </p>
                                    </div>
                                    <div class="relative">
                                        <input type="number"
                                            name="normal_ranges[{{ $index }}][age_max]"
                                            value="{{ old("normal_ranges.$index.age_max", $range['age_max']) }}"
                                            class="w-full px-3 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                                            onchange="updateAgeDisplay(this, {{ $index }}, 'max')"
                                            placeholder="jours">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-medium">j</span>
                                    </div>
                                </div>

                                {{-- Normal Min --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Min</label>
                                    <div class="relative">
                                        <input type="number" 
                                            step="0.01"
                                            name="normal_ranges[{{ $index }}][normal_min]"
                                            value="{{ old("normal_ranges.$index.normal_min", $range['normal_min']) }}"
                                            class="w-full px-3 pr-16 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                                            placeholder="0.00">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded" id="unit-display-min-{{ $index }}">
                                            {{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Normal Max --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Max</label>
                                    <div class="relative">
                                        <input type="number" 
                                            step="0.01"
                                            name="normal_ranges[{{ $index }}][normal_max]"
                                            value="{{ old("normal_ranges.$index.normal_max", $range['normal_max']) }}"
                                            class="w-full px-3 pr-16 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                                            placeholder="0.00">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded" id="unit-display-max-{{ $index }}">
                                            {{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Pregnancy --}}
                                <div class="flex flex-col items-center justify-end space-y-2">
                                    <label class="flex items-center gap-2 px-3 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-pink-50 hover:border-pink-300 transition-all">
                                        <input type="checkbox"
                                            name="normal_ranges[{{ $index }}][pregnant_applicable]"
                                            value="1"
                                            {{ old("normal_ranges.$index.pregnant_applicable", $range['pregnant_applicable'] ?? false) ? 'checked' : '' }}
                                            class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                                        <span class="text-sm font-medium text-gray-700">🤰 Grossesse</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Delete Button --}}
                            <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
                                <button type="button"
                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette plage ?')) this.closest('.bg-gradient-to-r').remove();"
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


            {{-- Formula Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Calculation Formula
                        </h2>
                        <span class="text-xs bg-gray-200 text-gray-600 px-2.5 py-1 rounded-full font-medium">Optional</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Formula</label>
                    <textarea name="formula" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-gray-50 hover:bg-white font-mono text-sm"
                              placeholder="Enter calculation formula (e.g., value1 + value2 * 0.5)">{{ old('formula', $analysis['formula']) }}</textarea>
                    @error('formula')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                        <input type="hidden" name="is_active" value="0">
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

{{-- JavaScript --}}
<script>
let rangeCounter = {{ count($analysis['normal_ranges'] ?? []) }};
let currentUnit = '{{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}';

// Update unit display when unit dropdown changes
document.querySelector('select[name="unit_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const unitText = selectedOption.text;
    currentUnit = unitText;
    
    // Update all existing unit displays
    document.querySelectorAll('[id^="unit-display-"]').forEach(span => {
        span.textContent = unitText;
    });
});

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

function updateAgeDisplay(input, index, type) {
    const days = parseInt(input.value) || null;
    const displayId = `age-${type}-display-${index}`;
    const displayElement = document.getElementById(displayId);
    if (displayElement) {
        displayElement.textContent = formatAgeClinicalJS(days);
    }
}

function addNormalRange() {
    const container = document.getElementById('normalRangesContainer');
    const index = rangeCounter++;

    const div = document.createElement('div');
    div.className = 'bg-gradient-to-r from-gray-50 to-blue-50 border-2 border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200';

    div.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Sexe</label>
                <select name="normal_ranges[${index}][sex_applicable]" 
                        class="w-full px-3 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium">
                    <option value="All">Tous</option>
                    <option value="M">Homme</option>
                    <option value="F">Femme</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Âge Min</label>
                <div class="bg-blue-50 rounded-lg p-2 mb-2 min-h-[44px] flex items-center justify-center">
                    <p class="text-xs font-semibold text-blue-700 text-center" id="age-min-display-${index}">∞</p>
                </div>
                <div class="relative">
                    <input type="number"
                        name="normal_ranges[${index}][age_min]"
                        class="w-full px-3 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                        onchange="updateAgeDisplay(this, ${index}, 'min')"
                        placeholder="jours">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-medium">j</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Âge Max</label>
                <div class="bg-blue-50 rounded-lg p-2 mb-2 min-h-[44px] flex items-center justify-center">
                    <p class="text-xs font-semibold text-blue-700 text-center" id="age-max-display-${index}">∞</p>
                </div>
                <div class="relative">
                    <input type="number"
                        name="normal_ranges[${index}][age_max]"
                        class="w-full px-3 py-2 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                        onchange="updateAgeDisplay(this, ${index}, 'max')"
                        placeholder="jours">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-medium">j</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Min</label>
                <div class="relative">
                    <input type="number" step="0.01"
                        name="normal_ranges[${index}][normal_min]"
                        class="w-full px-3 pr-16 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                        placeholder="0.00">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded" id="unit-display-min-${index}">
                        ${currentUnit}
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Val. Max</label>
                <div class="relative">
                    <input type="number" step="0.01"
                        name="normal_ranges[${index}][normal_max]"
                        class="w-full px-3 pr-16 py-2.5 text-sm text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white font-medium"
                        placeholder="0.00">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded" id="unit-display-max-${index}">
                        ${currentUnit}
                    </span>
                </div>
            </div>
            
            <div class="flex flex-col items-center justify-end space-y-2">
                <label class="flex items-center gap-2 px-3 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-pink-50 hover:border-pink-300 transition-all">
                    <input type="checkbox"
                        name="normal_ranges[${index}][pregnant_applicable]"
                        value="1"
                        class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                    <span class="text-sm font-medium text-gray-700">🤰 Grossesse</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
            <button type="button"
                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette plage ?')) this.closest('.bg-gradient-to-r').remove();"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 text-sm font-medium border border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Supprimer
            </button>
        </div>
    `;

    container.appendChild(div);
}
</script>
@endsection
