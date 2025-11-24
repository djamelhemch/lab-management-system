@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Détails de la Convention</h1>

    <div class="bg-white p-6 rounded shadow space-y-4">
        <div>
            <span class="font-semibold text-gray-700">Type de remise :</span>
            <span>{{ ucfirst($agreement['discount_type']) }}</span>
        </div>

        <div>
            <span class="font-semibold text-gray-700">Valeur de remise :</span>
            <span>{{ $agreement['discount_value'] }}</span>
        </div>

        <div>
            <span class="font-semibold text-gray-700">Statut :</span>
            <span class="{{ $agreement['status'] === 'active' ? 'text-green-600' : 'text-gray-600' }}">
                {{ ucfirst($agreement['status']) }}
            </span>
        </div>

        <div>
            <span class="font-semibold text-gray-700">Description :</span>
            <p class="mt-1">{{ $agreement['description'] ?? '-' }}</p>
        </div>

        <div>
            <span class="font-semibold text-gray-700">Créée le :</span>
            <span>{{ \Carbon\Carbon::parse($agreement['created_at'])->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="mt-6 flex space-x-2">
        <a href="{{ route('agreements.edit', $agreement['id']) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">
            Modifier
        </a>
        <form action="{{ route('agreements.destroy', $agreement['id']) }}" method="POST" onsubmit="return confirm('Supprimer cette convention ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                Supprimer
            </button>
        </form>
        <a href="{{ route('agreements.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
            Retour
        </a>
    </div>
</div>
@endsection
