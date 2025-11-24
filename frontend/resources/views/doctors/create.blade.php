@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <h2 class="text-3xl font-semibold text-gray-800 mb-8">Add New Doctor</h2>

    @if(session('error'))
        <div class="mb-4 text-red-600">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('doctors.store') }}" class="bg-white rounded-lg shadow p-8 space-y-6">
        @csrf

        <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input id="full_name" name="full_name" type="text" required
                class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('full_name') }}">
            @error('full_name')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
            <input id="specialty" name="specialty" type="text"
                class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('specialty') }}">
            @error('specialty')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input id="phone" name="phone" type="text"
                class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('phone') }}">
            @error('phone')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" name="email" type="email"
                class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('email') }}">
            @error('email')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input id="address" name="address" type="text"
                class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('address') }}">
            @error('address')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="is_prescriber" id="is_prescriber"
                class="h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                {{ old('is_prescriber') ? 'checked' : '' }}>
            <label for="is_prescriber" class="ml-2 block text-sm text-gray-700">Is Prescriber</label>
        </div>

        <div>
            <button type="submit" class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-700 transition">
                Save Doctor
            </button>
        </div>
    </form>

    @if ($errors->any())
        <div class="mt-6 text-red-600">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection