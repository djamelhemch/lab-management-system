{{-- resources/views/analyses/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Analysis Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $analysis['name'] ?? ($analysis->name ?? 'N/A') }}</h2>
            <p class="text-gray-600">Analysis Details</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('analyses.edit', $analysis['id']) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                Edit
            </a>
            <a href="{{ route('analyses.index') }}" 
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        {{-- Header --}}
        <div class="bg-gray-50 px-6 py-4 border-b">
            <div class="bg-gray-50 px-6 py-4 border-b">  
                <div class="flex justify-between items-center">  
                    <div>  
                        <h3 class="text-lg font-medium text-gray-900">{{ is_string($analysis['name']) ? $analysis['name'] : 'N/A' }}</h3>  
                        @if($analysis['code'])  
                            <p class="text-sm text-gray-500">Code: {{ is_string($analysis['code']) ? $analysis['code'] : 'N/A' }}</p>  
                        @endif  
                    </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-green-600">  
                        {{ is_numeric($analysis['price']) ? number_format($analysis['price'], 2) : 'N/A' }}  DZD
                    </div>
                    @if($analysis['is_active'] ?? true)  
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">  
                    Active  
                </span>  
                    @else  
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">  
                            Inactive  
                        </span>  
                    @endif  
                </div>  
            </div>  
        </div>

        {{-- Details --}}
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Basic Information --}}
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-3">Basic Information</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-sm text-gray-900">
                               @if(!empty($analysis['category_analyse']['name']))  
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">  
                                        {{ $analysis['category_analyse']['name'] }}  
                                    </span>  
                                @else  
                                    <span class="text-gray-400">Not specified</span>  
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unit</dt>
                            <dd class="text-sm text-gray-900">  
                                @php  
                                    $unitName = $analysis['unit']['name'] ?? null;  
                                @endphp  
                                @if(is_array($unitName))  
                                    {{ implode(', ', $unitName) }}  
                                @elseif(is_string($unitName))  
                                    {{ $unitName }}  
                                @else  
                                    Not specified  
                                @endif  
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sample Type</dt>
                            <dd class="text-sm text-gray-900">  
                                {{ $analysis['sample_type']['name'] ?? 'Not specified' }}  
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Specifications --}}
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-3">Specifications</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sex Applicable</dt>
                            <dd class="text-sm text-gray-900">
                                @switch($analysis['sex_applicable'])
                                    @case('M')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Male Only
                                        </span>
                                        @break
                                    @case('F')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                            Female Only
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            All
                                        </span>
                                @endswitch
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Age Range</dt>
                            <dd class="text-sm text-gray-900">
                                @if($analysis['age_min'] || $analysis['age_max'])
                                    {{ $analysis['age_min'] ?? 0 }} - {{ $analysis['age_max'] ?? '∞' }} years
                                @else
                                    All ages
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pregnancy Applicable</dt>
                            <dd class="text-sm text-gray-900">
                                @if($analysis['pregnant_applicable'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Yes
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        No
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Normal Range --}}
            @if($analysis['normal_min'] || $analysis['normal_max'])  
            <div class="mt-6 pt-6 border-t">  
                <h4 class="text-md font-medium text-gray-900 mb-3">Normal Range</h4>  
                <div class="bg-gray-50 rounded-lg p-4">  
                    <div class="flex items-center justify-between">  
                        <span class="text-sm text-gray-600">Normal Values:</span>  
                        <span class="text-sm font-medium text-gray-900">  
                            {{ $analysis['normal_min'] ?? 'N/A' }} - {{ $analysis['normal_max'] ?? 'N/A' }}  
                            @if(!empty($analysis['unit']['name']))  
                                {{ $analysis['unit']['name'] }}  
                            @endif  
                        </span>  
                    </div>  
                </div>  
            </div>  
        @endif

            {{-- Formula --}}
            @if($analysis['formula'])  
            <div class="mt-6 pt-6 border-t">  
                <h4 class="text-md font-medium text-gray-900 mb-3">Calculation Formula</h4>  
                <div class="bg-gray-50 rounded-lg p-4">  
                    <code class="text-sm text-gray-800">{{ is_string($analysis['formula']) ? $analysis['formula'] : 'N/A' }}</code>  
                </div>  
            </div>  
        @endif
        </div>
    </div>
</div>
@endsection