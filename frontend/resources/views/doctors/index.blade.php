@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Doctors</h2>
    <a href="{{ route('doctors.create') }}"
       class="btn-primary">
        + Add New Doctor
    </a>
</div>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if (count($doctors) === 0)
    <div class="text-center text-gray-600">
        <p>No doctors found.</p>
        <a href="{{ route('doctors.create') }}" class="btn-primary mt-4 inline-block">
            Create Your First Doctor
        </a>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded shadow">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="table-header">Name</th>
                    <th class="table-header">Specialty</th>
                    <th class="table-header">Phone</th>
                    <th class="table-header">Email</th>
                    <th class="table-header">Prescriber</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                @foreach ($doctors as $doctor)
                    <tr class="hover:bg-gray-50 border-t">
                        <td class="table-cell">{{ $doctor['full_name'] }}</td>
                        <td class="table-cell">{{ $doctor['specialty'] ?? '-' }}</td>
                        <td class="table-cell">{{ $doctor['phone'] ?? '-' }}</td>
                        <td class="table-cell">{{ $doctor['email'] ?? '-' }}</td>
                        <td class="table-cell">
                            {{ $doctor['is_prescriber'] ? 'Yes' : 'No' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<style>
    .btn-primary {
        @apply bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition duration-150;
    }
    .table-header {
        @apply text-left px-4 py-2 text-sm font-medium;
    }
    .table-cell {
        @apply px-4 py-2 text-sm;
    }
</style>
@endsection
