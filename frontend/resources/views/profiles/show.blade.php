@extends('layouts.app')
@php
    $filename = $profile['photo_url'] ?? null; // "user_5.png"
    $photoUrl = $filename
        ? asset('storage/profile_photos/' . $filename)
        : 'https://ui-avatars.com/api/?name=' . urlencode($name ?? 'User') . '&size=150&background=6366f1&color=fff';
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">

        {{-- Profile Header --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-200">
            <div class="relative w-36 h-36 rounded-full border-4 border-indigo-100 overflow-hidden shrink-0 shadow-lg">
                <img id="profile_preview"
                        src="{{ $photoUrl }}"
                        class="w-full h-full object-cover">
            </div>

            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold">{{ $name ?? 'User' }}</h1>
                <p class="text-gray-500">{{ $profile['email'] ?? '' }}</p>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-6">Profile Settings</h2>

        {{-- FORM --}}
        <form method="POST" action="{{ route('profiles.update', $profile['user_id']) }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Photo Upload --}}
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <input 
                    id="photo_file"
                    type="file"
                    name="photo_file"
                    accept="image/*"
                    class="flex-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                >

                <label for="photo_file"
                       class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl cursor-pointer transition-all hover:scale-105 shadow-md">
                    📸 Change Photo
                </label>
            </div>

            {{-- Settings --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium mb-2">Theme</label>
                    <select name="theme" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm focus:ring-indigo-500">
                        <option value="light" {{ ($profile['theme'] ?? 'light') === 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark"  {{ ($profile['theme'] ?? '') === 'dark'  ? 'selected' : '' }}>Dark</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Goals</label>
                    <textarea name="goals" rows="4" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm">
                        {{ implode(', ', $profile['goals'] ?? []) }}
                    </textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Checklist</label>
                    <textarea name="checklist" rows="4" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm">
                        {{ implode(', ', $profile['checklist'] ?? []) }}
                    </textarea>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                        class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-lg transition-all hover:scale-105">
                    💾 Save Changes
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('photo_file');
    const preview   = document.getElementById('profile_preview');

    fileInput.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = ev => preview.src = ev.target.result;
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
