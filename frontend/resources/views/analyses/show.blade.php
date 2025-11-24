@extends('layouts.app')

@section('title', 'Analysis Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $analysis['name'] ?? 'N/A' }}</h1>
            <p class="text-sm text-gray-500">Detailed information about this laboratory analysis</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('analyses.edit', $analysis['id']) }}" 
               class="px-4 py-2 bg-[#bc1622] text-white rounded-lg shadow hover:bg-[#a0131e] transition">
                ✏️ Edit
            </a>
            <a href="{{ route('analyses.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition">
                ← Back
            </a>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">

        {{-- Header Info --}}
        <div class="bg-gray-50 px-6 py-5 border-b flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $analysis['code'] ?? '—' }}</h2>
                <p class="text-sm text-gray-500">Analysis Code</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-extrabold text-[#bc1622]">
                    {{ is_numeric($analysis['price']) ? number_format($analysis['price'], 2) : 'N/A' }} DZD
                </div>
                <span class="inline-flex mt-1 items-center px-3 py-0.5 rounded-full text-xs font-medium 
                             {{ ($analysis['is_active'] ?? true) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ($analysis['is_active'] ?? true) ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- Body Content --}}
        <div class="px-6 py-6 grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Left: Basic Info --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-[#bc1622]" /> Basic Information
                </h3>

                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd>
                            @if(!empty($analysis['category_analyse']['name']))
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                    {{ $analysis['category_analyse']['name'] }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">Not specified</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit</dt>
                        <dd class="text-gray-800 text-sm">
                            {{ $analysis['unit']['name'] ?? 'Not specified' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sample Type</dt>
                        <dd class="text-gray-800 text-sm">
                            {{ $analysis['sample_type']['name'] ?? 'Not specified' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tube Type</dt>
                        <dd>
                            @if(!empty($analysis['tube_type']))
                                <span class="inline-block bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                    {{ $analysis['tube_type'] }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">Not specified</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Right: Devices --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 text-[#bc1622]" /> Compatible Devices
                </h3>

                @if(!empty($analysis['device_names']) && count($analysis['device_names']) > 0)
                    <ul class="border border-gray-200 rounded-xl divide-y divide-gray-100">
                        @foreach($analysis['device_names'] as $device)
                            <li class="px-4 py-3 flex justify-between items-center hover:bg-gray-50 transition">
                                <span class="font-medium text-gray-800">{{ $device }}</span>
                                <span class="text-xs text-gray-500">Lab Device</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm">No compatible devices assigned.</p>
                @endif
            </div>
        </div>

        {{-- Normal Ranges --}}
        @if(!empty($analysis['normal_ranges']) && count($analysis['normal_ranges']) > 0)
        <div class="px-6 py-6 border-t bg-gray-50">
            
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-[#bc1622]" /> Normal Ranges
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-xl shadow-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Sex</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Age Range</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Pregnancy</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-800">Normal Range</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($analysis['normal_ranges'] as $range)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    @switch($range['sex_applicable'])
                                        @case('M') <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Male</span> @break
                                        @case('F') <span class="bg-pink-100 text-pink-800 px-2 py-1 rounded text-xs">Female</span> @break
                                        @default <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">All</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    {{ $range['age_min'] ?? 0 }} – {{ $range['age_max'] ?? '∞' }} yrs
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    @if($range['pregnant_applicable'])
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Yes</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center font-semibold text-[#bc1622]">
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
        @if(!empty($analysis['formula']))
        <div class="px-6 py-6 border-t">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <x-heroicon-o-calculator class="w-5 h-5 text-[#bc1622]" /> Calculation Formula
            </h3>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <code class="text-sm text-gray-800">{{ $analysis['formula'] }}</code>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
