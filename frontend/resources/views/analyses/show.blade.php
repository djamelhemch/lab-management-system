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

               
         {{-- Normal Ranges --}}
@if(!empty($analysis['normal_ranges']) && count($analysis['normal_ranges']) > 0)
    <div class="mt-6 pt-8 border-t">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">Normal Ranges</h4>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Sex</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Age Range</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Pregnancy</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-800">Normal Range</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($analysis['normal_ranges'] as $range)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Sex --}}
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                @switch($range['sex_applicable'])
                                    @case('M')
                                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs">Male</span>
                                        @break
                                    @case('F')
                                        <span class="px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs">Female</span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">All</span>
                                @endswitch
                            </td>

                            {{-- Age --}}
                            <td class="px-6 py-3 text-sm text-gray-700">
                                @if($range['age_min'] || $range['age_max'])
                                    {{ $range['age_min'] ?? 0 }} – {{ $range['age_max'] ?? '∞' }} yrs
                                @else
                                    All ages
                                @endif
                            </td>

                            {{-- Pregnancy --}}
                            <td class="px-6 py-3 text-sm">
                                @if($range['pregnant_applicable'])
                                    <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Yes</span>
                                @else
                                    <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">No</span>
                                @endif
                            </td>

                            {{-- Normal Range --}}
                            <td class="px-6 py-3 text-center text-base font-bold text-green-700">
                                {{ $range['normal_min'] ?? 'N/A' }} – {{ $range['normal_max'] ?? 'N/A' }}
                            </td>      
                        </tr>
                    @endforeach
                </tbody>
            </table>
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