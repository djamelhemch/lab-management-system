@extends('layouts.app')

@section('title', 'Doctors')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Doctors</h2>
    <a href="{{ route('doctors.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
        + Add New Doctor
    </a>
</div>

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if (empty($doctors))
    <div class="text-center text-gray-600">
        <p>No doctors found.</p>
        <a href="{{ route('doctors.create') }}" class="mt-4 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Create Your First Doctor
        </a>
    </div>
@else
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-sm font-medium text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Specialty</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Prescriber</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @foreach ($doctors as $doctor)
                    <tr class="hover:bg-gray-50 border-t">
                        <td class="px-4 py-3">{{ $doctor['full_name'] }}</td>
                        <td class="px-4 py-3">{{ $doctor['specialty'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $doctor['phone'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $doctor['email'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 rounded-full text-xs {{ $doctor['is_prescriber'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $doctor['is_prescriber'] ? 'Yes' : 'No' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
