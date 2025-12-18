
@extends('layouts.app')

@section('content')
@php
    $labels = [
        'marquee_banner' => 'Queue marquee banner',
        'queue_video'    => 'Queue video',
    ];
@endphp

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
<div class="space-y-10">
    @php
        $labels = [
            'marquee_banner' => 'Queue marquee banner',
            'queue_video'    => 'Queue video',
            'currency'       => 'Default currency',
        ];
    @endphp

    @foreach($groupedSettings as $groupKey => $settings)
        @if(count($settings))
            {{-- Group title --}}
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    {{ $groupLabels[$groupKey] ?? ucfirst($groupKey) }}
                </h2>

                <div class="space-y-6">
                    @foreach($settings as $setting)
                        <div x-data="{ open: false }"
                             class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">

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
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            {{ $labels[$setting['name']] ?? ucfirst(str_replace('_', ' ', $setting['name'])) }}
                                        </h3>
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

                                    @if($setting['name'] === 'queue_video')
    @foreach($setting['options'] as $option)
        @if($option['is_default'])
            <div x-data="{ uploading: false, fileName: '', hasFile: false }" class="space-y-5">
                {{-- Current path --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Current video path
                    </label>
                    <div class="bg-white border border-gray-200 rounded-xl p-3 text-sm text-gray-800">
                        {{ $option['value'] }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        This URL is used on the waiting room display screen.
                    </p>
                </div>

                {{-- Preview --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Preview
                    </label>
                    <div class="bg-black rounded-xl overflow-hidden">
                        <video controls muted class="w-full">
                            <source src="{{ $option['value'] }}" type="video/mp4">
                            Video preview not available.
                        </video>
                    </div>
                </div>

                {{-- Upload form with spinner --}}
                <form method="POST"
                      action="{{ route('admin.settings.updateVideo') }}"
                      enctype="multipart/form-data"
                      class="space-y-3"
                      x-on:submit="if (!hasFile) { $event.preventDefault(); return; } uploading = true;">
                    @csrf
                    <input type="hidden" name="setting_id" value="{{ $setting['id'] }}">
                    <input type="hidden" name="option_id" value="{{ $option['id'] }}">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Upload new video (mp4)
                        </label>

                        <label class="flex items-center justify-between border border-dashed border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:border-[#bc1622] hover:bg-red-50/40 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-[#bc1622]/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#bc1622]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4-4m0 0l-4 4m4-4v12"/>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-800">
                                        <span x-text="fileName || 'Choose a video file'"></span>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        MP4 only, max 50 MB
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs text-[#bc1622] font-semibold" x-show="!hasFile">Browse</span>
                            <span class="text-xs text-green-600 font-semibold" x-show="hasFile">Ready</span>
                            <input type="file"
                                   name="video"
                                   accept="video/mp4"
                                   class="hidden"
                                   x-on:change="
                                        if ($event.target.files.length) {
                                            fileName = $event.target.files[0].name;
                                            hasFile = true;
                                        } else {
                                            fileName = '';
                                            hasFile = false;
                                        }
                                   ">
                        </label>

                        @error('video')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                :disabled="uploading || !hasFile"
                                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white 
                                       transition
                                       disabled:opacity-60 disabled:cursor-not-allowed
                                       bg-[#bc1622] hover:bg-red-700"
                        >
                            <svg x-show="uploading"
                                 class="w-4 h-4 mr-2 animate-spin text-white"
                                 fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                            </svg>
                            <span x-show="!uploading">Upload & replace video</span>
                            <span x-show="uploading">Uploading...</span>
                        </button>

                        <p class="text-xs text-gray-500" x-show="!uploading">
                            Existing video will be removed before saving the new one.
                        </p>
                        <p class="text-xs text-gray-500" x-show="uploading">
                            Please wait, this may take a few seconds depending on file size.
                        </p>
                    </div>
                </form>
            </div>
        @endif
         @endforeach

                                 @elseif($setting['name'] === 'marquee_banner')
                    {{-- Special handling for marquee banner - inline edit --}}
                    @foreach($setting['options'] as $option)
                        @if($option['is_default'])
                            <div x-data="{ editing: false }" class="space-y-4">
                                {{-- Display Mode --}}
                                <div x-show="!editing">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Banner Text</label>
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                                        <p class="text-gray-800">{{ $option['value'] }}</p>
                                    </div>
                                    <button @click="editing = true" type="button"
                                        class="mt-3 flex items-center px-4 py-2 bg-[#bc1622] text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                </div>

                                {{-- Edit Mode --}}
                                <div x-show="editing">
                                    <form method="POST" action="{{ route('admin.settings.update') }}">
                                        @csrf
                                        <input type="hidden" name="setting_id" value="{{ $setting['id'] }}">
                                        <input type="hidden" name="option_id" value="{{ $option['id'] }}">
                                        
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Edit Banner Text</label>
                                        <textarea 
                                            name="marquee_text" 
                                            rows="4"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622] transition-colors resize-none"
                                        >{{ $option['value'] }}</textarea>
                                        
                                        <div class="flex gap-2 mt-3">
                                            <button type="submit"
                                                class="flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Save
                                            </button>
                                            <button @click="editing = false" type="button"
                                                class="flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                            @else
                    {{-- Regular settings with add/delete options --}}

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
                                    <form method="POST" action="{{ route('admin.settings.addOption', $setting['id']) }}"
                                          class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
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
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>


{{-- Alpine.js for collapsible behavior --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
