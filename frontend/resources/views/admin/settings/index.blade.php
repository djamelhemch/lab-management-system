@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <svg class="w-8 h-8 text-[#bc1622]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
                System Settings
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Manage your system configurations and default values.</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-600 text-green-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-600 text-red-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Settings List --}}
    <div class="space-y-6">
        @foreach($settings as $setting)
        <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">

            {{-- Header --}}
            <button @click="open = !open"
                class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 transition">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-[#bc1622]/10">
                        <svg class="w-5 h-5 text-[#bc1622]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">{{ ucfirst($setting['name']) }}</h2>
                        <p class="text-sm text-gray-500">Setting ID: {{ $setting['id'] }}</p>
                    </div>
                </div>
                <svg x-show="!open" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
                <svg x-show="open" class="w-5 h-5 text-gray-400 transition-transform duration-200 rotate-180" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Collapsible Content --}}
            <div x-show="open" x-collapse class="border-t border-gray-100 p-6 bg-gray-50">
                {{-- Options --}}
                <div class="space-y-3 mb-6">
                    @foreach($setting['options'] as $option)
                        <div class="flex flex-col md:flex-row md:items-center justify-between bg-white border border-gray-200 rounded-xl p-3 shadow-sm hover:border-[#bc1622]/40 transition">
                            <span class="font-medium text-gray-800">
                                {{ $option['value'] }}
                                @if($option['is_default'])
                                    <span class="ml-2 text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                        Default
                                    </span>
                                @endif
                            </span>

                            <div class="flex space-x-2 mt-2 md:mt-0">
                                {{-- Set Default --}}
                                @if(!$option['is_default'])
                                    <form method="POST" action="{{ route('admin.settings.setDefault', [$setting['id'], $option['id']]) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="flex items-center px-3 py-1.5 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Set Default
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.settings.deleteOption', $option['id']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center px-3 py-1.5 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Add New Option --}}
                <form method="POST" action="{{ route('admin.settings.addOption', $setting['id']) }}" class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    @csrf
                    <input type="text" name="value" placeholder="Enter new option..."
                           class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-[#bc1622] focus:border-[#bc1622] p-2"
                           required>

                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_default" value="1"
                               class="rounded border-gray-300 text-[#bc1622] focus:ring-[#bc1622]">
                        <span class="text-sm text-gray-700">Set as default</span>
                    </label>

                    <button type="submit"
                            class="flex items-center justify-center px-5 py-2 bg-[#bc1622] text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Option
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Alpine.js for collapsible behavior --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
