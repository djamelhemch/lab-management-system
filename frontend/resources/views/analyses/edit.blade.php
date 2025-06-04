{{-- resources/views/analyses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Analysis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Analysis</h2>
        <p class="text-gray-600">Update analysis information and pricing.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('analyses.update', $analysis['id']) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Information --}}
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" name="code" value="{{ old('code', $analysis['code']) }}" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @error('code')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name', $analysis['name']) }}" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" 
                               required>
                        @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_analyse_id" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">  
                            <option value="">Select Category</option>  
                            @foreach($categories as $category)  
                                <option value="{{ $category['id'] }}"  
                                    {{ old('category_analyse_id', $analysis['category_analyse']['id'] ?? null) == $category['id'] ? 'selected' : '' }}>  
                                    {{ $category['name'] }}  
                                </option>  
                            @endforeach  
                        </select>  
                        @error('category_analyse_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">DZD</span>
                            <input type="number" name="price" value="{{ old('price', $analysis['price']) }}" 
                                   step="0.01" min="0" 
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                        </div>
                        @error('price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <select name="unit_id" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Unit</option>  
                            @foreach($units as $unit)  
                                <option value="{{ $unit['id'] }}"  
                                    {{ old('unit_id', $analysis['unit']['id'] ?? null) == $unit['id'] ? 'selected' : '' }}>  
                                    {{ $unit['name'] }}  
                                </option>  
                            @endforeach  
                        </select>  
                        @error('unit_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Specifications</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sex Applicable</label>
                        <select name="sex_applicable" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="All" {{ old('sex_applicable', $analysis['sex_applicable']) == 'All' ? 'selected' : '' }}>All</option>
                            <option value="M" {{ old('sex_applicable', $analysis['sex_applicable']) == 'M' ? 'selected' : '' }}>Male Only</option>
                            <option value="F" {{ old('sex_applicable', $analysis['sex_applicable']) == 'F' ? 'selected' : '' }}>Female Only</option>
                        </select>
                        @error('sex_applicable')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age Min</label>
                        <input type="number" name="age_min" value="{{ old('age_min', $analysis['age_min']) }}" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @error('age_min')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age Max</label>
                        <input type="number" name="age_max" value="{{ old('age_max', $analysis['age_max']) }}" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @error('age_max')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sample Type</label>
                    <select name="sample_type_id" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Sample Type</option>  
                        @foreach($sampleTypes as $sampleType)  
                            <option value="{{ $sampleType['id'] }}"  
                                {{ old('sample_type_id', $analysis['sample_type']['id'] ?? null) == $sampleType['id'] ? 'selected' : '' }}>  
                                {{ $sampleType['name'] }}  
                            </option>  
                        @endforeach  
                    </select>  
                    @error('sample_type_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="pregnant_applicable" value="1" 
                               {{ old('pregnant_applicable', $analysis['pregnant_applicable']) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Applicable during pregnancy</label>
                    </div>
                </div>
            </div>

            {{-- Normal Range --}}
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Normal Range</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Normal Min</label>
                        <input type="number" name="normal_min" value="{{ old('normal_min', $analysis['normal_min']) }}" 
                               step="0.01" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @error('normal_min')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Normal Max</label>
                        <input type="number" name="normal_max" value="{{ old('normal_max', $analysis['normal_max']) }}" 
                               step="0.01" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @error('normal_max')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- Formula --}}
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Formula (Optional)</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calculation Formula</label>
                    <textarea name="formula" rows="3" 
                              class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">{{ old('formula', $analysis['formula']) }}</textarea>
                    @error('formula')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $analysis['is_active'] ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Active</label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('analyses.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Update Analysis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection