@extends('layouts.app')

@section('title', 'Analysis Details')

@php
function formatAgeClinical($days) {
    if ($days === null) return '∞';
    
    $years = floor($days / 365);
    $months = floor(($days % 365) / 30);
    $daysRemain = $days % 30;
    
    $parts = [];
    if ($years > 0) $parts[] = $years . ' an' . ($years > 1 ? 's' : '');
    if ($months > 0) $parts[] = $months . ' mois';
    if ($daysRemain > 0 && $years == 0) $parts[] = $daysRemain . ' j';
    
    return !empty($parts) ? implode(' ', $parts) : '0';
}
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-sm mb-3">
                        <a href="{{ route('analyses.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            Analyses
                        </a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Details</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Analysis Details</h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        View complete analysis information and specifications
                    </p>
                </div>
                <a href="{{ route('analyses.edit', $analysis['id']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 hover:shadow-lg transition-all duration-200 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Analysis
                </a>
            </div>
        </div>

        {{-- Overview Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Overview
                </h2>
            </div>
            
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 break-words">
                            {{ $analysis['name'] ?? 'N/A' }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-600">Code:</span>
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-900 rounded-lg font-mono text-sm font-semibold border border-gray-200">
                                    {{ $analysis['code'] ?? '—' }}
                                </span>
                            </div>
                            @if($analysis['is_active'] ?? true)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-50 text-green-700 border-2 border-green-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border-2 border-gray-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl px-6 py-5 border-2 border-blue-200 text-center shadow-sm hover:shadow-md transition-all">
                            <div class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-2">Price</div>
                            <div class="text-3xl font-bold text-blue-900">
                                {{ is_numeric($analysis['price']) ? number_format($analysis['price'], 2) : 'N/A' }}
                            </div>
                            <div class="text-sm text-blue-600 font-semibold mt-1">DZD</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Analysis Details
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Category --}}
                            <div>
                                <dt class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Category</dt>
                                <dd>
                                    @if(!empty($analysis['category_analyse']['name']))
                                        <span class="inline-flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg border border-gray-200">
                                            {{ $analysis['category_analyse']['name'] }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Not specified</span>
                                    @endif
                                </dd>
                            </div>

                            {{-- Unit --}}
                            <div>
                                <dt class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Unit</dt>
                                <dd>
                                    @if(!empty($analysis['unit']['name']))
                                        <span class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200">
                                            {{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Not specified</span>
                                    @endif
                                </dd>
                            </div>

                            {{-- Tube Type --}}
                            <div>
                                <dt class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Tube Type</dt>
                                <dd>
                                    @if(!empty($analysis['tube_type']))
                                        <span class="inline-flex items-center px-3 py-2 text-sm font-semibold text-orange-700 bg-orange-50 rounded-lg border border-orange-200">
                                            {{ $analysis['tube_type'] }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Not specified</span>
                                    @endif
                                </dd>
                            </div>

                            {{-- Sample Types --}}
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Sample Types</dt>
                                <dd class="flex flex-wrap gap-2">
                                    @if(!empty($analysis['sample_types']))
                                        @foreach($analysis['sample_types'] as $type)
                                            <span class="inline-flex items-center px-3 py-2 text-sm font-semibold text-purple-700 bg-purple-50 rounded-lg border border-purple-200">
                                                {{ $type['name'] }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-gray-400 italic">Not specified</span>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formula --}}
                @if(!empty($analysis['formula']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Calculation Formula
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg p-5 overflow-x-auto border-2 border-gray-700 shadow-inner">
                            <code class="text-sm font-mono text-green-400 break-all leading-relaxed">{{ $analysis['formula'] }}</code>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Normal Ranges --}}
                @if(!empty($analysis['normal_ranges']) && count($analysis['normal_ranges']) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Normal Ranges
                            <span class="ml-auto px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                {{ count($analysis['normal_ranges']) }}
                            </span>
                        </h2>
                    </div>
                    
                    {{-- Mobile: Card View --}}
                    <div class="block lg:hidden divide-y divide-gray-200">
                        @foreach($analysis['normal_ranges'] as $index => $range)
                        <div class="p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center justify-center w-7 h-7 bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-xs rounded-full shadow-sm">
                                    #{{ $index + 1 }}
                                </span>
                                <div class="flex gap-2">
                                    @switch($range['sex_applicable'])
                                        @case('M')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                </svg>
                                                Male
                                            </span>
                                            @break
                                        @case('F')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-pink-50 text-pink-700 border border-pink-200">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                </svg>
                                                Female
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                                </svg>
                                                All
                                            </span>
                                    @endswitch
                                    
                                    @if($range['pregnant_applicable'])
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            Pregnancy
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">Age Range</span>
                                <p class="text-sm font-mono text-blue-900 font-semibold mt-1">
                                    {{ formatAgeClinical($range['age_min'] ?? 0) }} → {{ isset($range['age_max']) ? formatAgeClinical($range['age_max']) : '∞' }}
                                </p>
                            </div>
                            
                            <div class="p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                <span class="text-xs font-bold text-green-600 uppercase tracking-wide block mb-1">Normal Range</span>
                                <p class="text-lg font-bold text-green-900">
                                    {{ number_format($range['normal_min'] ?? 0, 2) }} – {{ number_format($range['normal_max'] ?? 0, 2) }}
                                    <span class="text-sm text-green-700 font-semibold">{{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}</span>
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Desktop: Table View --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sex</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Age Range</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pregnancy</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Normal Range</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($analysis['normal_ranges'] as $index => $range)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-xs rounded-full shadow-sm">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($range['sex_applicable'])
                                            @case('M')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                    </svg>
                                                    Male
                                                </span>
                                                @break
                                            @case('F')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold bg-pink-50 text-pink-700 border border-pink-200">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                    </svg>
                                                    Female
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                                    </svg>
                                                    All
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1.5 bg-blue-50 text-blue-900 rounded-lg text-sm font-mono font-semibold border border-blue-200">
                                            {{ formatAgeClinical($range['age_min'] ?? 0) }} → {{ isset($range['age_max']) ? formatAgeClinical($range['age_max']) : '∞' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($range['pregnant_applicable'])
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                Yes
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ number_format($range['normal_min'] ?? 0, 2) }} – {{ number_format($range['normal_max'] ?? 0, 2) }}
                                        </span>
                                        <span class="text-xs text-gray-600 font-semibold ml-1">
                                            {{ $analysis['unit']['symbol'] ?? $analysis['unit']['name'] ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>

            {{-- Right Column (1/3) - Sidebar --}}
            <div class="space-y-6">

                {{-- Compatible Devices --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            Compatible Devices
                            @if(!empty($analysis['device_names']) && count($analysis['device_names']) > 0)
                                <span class="ml-auto px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                    {{ count($analysis['device_names']) }}
                                </span>
                            @endif
                        </h2>
                    </div>
                    <div class="p-4">
                        @if(!empty($analysis['device_names']) && count($analysis['device_names']) > 0)
                            <ul class="space-y-2">
                                @foreach($analysis['device_names'] as $device)
                                    <li class="flex items-start gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition-all border border-gray-200">
                                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-700 break-words">{{ $device }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-600 mb-1">No devices assigned</p>
                                <p class="text-xs text-gray-500">This analysis is not linked to any device</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Quick Actions
                        </h2>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('analyses.edit', $analysis['id']) }}" 
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-100 hover:border-blue-300 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Analysis
                        </a>
                        <a href="{{ route('analyses.index') }}" 
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
