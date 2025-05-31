@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold text-gray-800">Patients</h2>
    <a href="{{ route('patients.create') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">+ Add Patient</a>
</div>

<table class="w-full table-auto border-collapse bg-white shadow-md rounded overflow-hidden">
    <thead class="bg-gray-100 text-gray-700">
        <tr>
            <th class="p-3 text-left">Dossier N°</th>
            <th class="p-3 text-left">Nom Prénom</th>
            <th class="p-3 text-left">Sexe</th>
            <th class="p-3 text-left">Médecin Traitant</th>
            <th class="p-3 text-left">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($patients as $p)
        <tr class="border-b hover:bg-gray-50">
            <td class="p-3">{{ $p['file_number'] }}</td>
            <td class="p-3">{{ $p['first_name'] }} {{ $p['last_name'] }}</td>
            <td class="p-3">{{ $p['gender'] }}</td>
            <td class="p-3">{{ $p['doctor_id'] ?? 'N/A' }}</td>
            <td class="p-3">
                <a href="{{ route('patients.show', $p['id']) }}" class="text-blue-600 hover:underline">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
