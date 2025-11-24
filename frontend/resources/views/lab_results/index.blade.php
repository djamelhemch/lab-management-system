@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Résultats du Laboratoire</h1>

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg shadow">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="mb-4 flex justify-between items-center">
        <input type="text" placeholder="Rechercher un résultat..." 
               class="border border-gray-300 rounded-lg px-3 py-2 w-1/3 focus:ring-2 focus:ring-red-500 outline-none">
    <a href="{{ route('lab-results.index') }}" 
        class="text-sm text-gray-500 hover:text-red-600 transition">🔄 Actualiser</a>
    </div>

    {{-- Results Table --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left">Patient</th>
                    <th class="px-4 py-3 text-left">N° Dossier</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Analyse</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Résultat</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Plage Normale</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Appareil</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Statut</th>
                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Date</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($labResults as $result)
                    @php
                        $isCritical = strtolower($result['interpretation'] ?? '') === 'critical';
                    @endphp
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-4 py-2">{{ $result['patient_first_name'] ?? '' }} {{ $result['patient_last_name'] ?? '' }}</td>
                        <td class="px-4 py-2 font-mono text-gray-600">{{ $result['file_number'] ?? '—' }}</td>
                        <td class="py-3 px-4 text-gray-800 font-medium">
                            {{ $result['analysis_code'] ?? '—' }}
                            <span class="text-gray-500 text-sm">({{ $result['analysis_name'] ?? '' }})</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold {{ $isCritical ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $result['result_value'] ?? '—' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            @if($result['normal_min'] !== null && $result['normal_max'] !== null)
                                {{ $result['normal_min'] }} - {{ $result['normal_max'] }}
                            @else
                                <span class="text-gray-400 italic">N/A</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-700">{{ $result['device_name'] ?? '—' }}</td>
                        <td class="py-3 px-4">
                            @if($isCritical)
                                <span class="bg-red-100 text-red-700 px-2 py-1 text-xs font-semibold rounded-lg">Critique</span>
                            @elseif($result['status'] === 'final')
                                <span class="bg-green-100 text-green-700 px-2 py-1 text-xs font-semibold rounded-lg">Final</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 text-xs font-semibold rounded-lg">En Cours</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($result['created_at'])->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('lab-results.show', $result['id']) }}" 
                               class="text-blue-600 hover:underline text-sm">Détails</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">Aucun résultat disponible</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
