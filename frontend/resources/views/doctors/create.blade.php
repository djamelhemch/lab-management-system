@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Add New Doctor</h2>

@if(session('error'))
    <div class="mb-4 text-red-600">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('doctors.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf

    <input name="full_name" placeholder="Full Name" required class="input" value="{{ old('full_name') }}">
    <input name="specialty" placeholder="Specialty" class="input" value="{{ old('specialty') }}">
    <input name="phone" placeholder="Phone" class="input" value="{{ old('phone') }}">
    <input name="email" placeholder="Email" class="input" value="{{ old('email') }}">
    <input name="address" placeholder="Address" class="input" value="{{ old('address') }}">

    <div class="flex items-center space-x-2">
        <input type="checkbox" name="is_prescriber" id="is_prescriber" class="h-5 w-5" {{ old('is_prescriber') ? 'checked' : '' }}>
        <label for="is_prescriber" class="text-gray-700">Is Prescriber</label>
    </div>

    <div class="md:col-span-2">
        <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
            Save Doctor
        </button>
    </div>
</form>

@if ($errors->any())
    <div class="mt-4 md:col-span-2 text-red-600">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    .input {
        @apply p-3 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-red-500;
    }

    .btn-primary {
        @apply bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition duration-150;
    }

    .form-checkbox {
        @apply rounded border-gray-300 focus:ring-red-500;
    }

</style>

</style>
@endsection
