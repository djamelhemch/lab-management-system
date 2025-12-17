@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <svg class="w-8 h-8 text-[#bc1622]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                System Settings
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Edit the marquee banner text displayed on the waiting room screen.</p>
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

    {{-- Marquee Banner Settings --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-[#bc1622]/10">
                    <svg class="w-6 h-6 text-[#bc1622]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Marquee Banner</h2>
                    <p class="text-sm text-gray-500">Update the scrolling text shown in the waiting room</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6">
            @csrf
            
            <input type="hidden" name="setting_id" value="{{ $settingId }}">
            <input type="hidden" name="option_id" value="{{ $optionId }}">
            
            <div class="mb-6">
                <label for="marquee_text" class="block text-sm font-semibold text-gray-700 mb-2">
                    Banner Text
                </label>
                <textarea 
                    id="marquee_text" 
                    name="marquee_text" 
                    rows="4"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622] transition-colors resize-none"
                    placeholder="Enter the marquee banner text..."
                >{{ old('marquee_text', $marqueeText) }}</textarea>
                
                @error('marquee_text')
                    <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </span>
                @enderror
                
                <p class="text-xs text-gray-500 mt-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    This text will scroll continuously on the waiting room display screen.
                </p>
            </div>

            {{-- Preview Box --}}
            <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Preview</label>
                <div class="bg-red-500 text-white py-3 px-4 rounded-lg overflow-hidden">
                    <div class="whitespace-nowrap text-sm font-semibold animate-marquee">
                        {{ $marqueeText ?: 'Your marquee text will appear here...' }}
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.settings.index') }}"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-[#bc1622] text-white rounded-lg hover:bg-red-700 transition font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    display: inline-block;
    animation: marquee 15s linear infinite;
}
</style>
@endsection
