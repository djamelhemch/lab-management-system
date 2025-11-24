@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Conventions</h1>
        <a href="{{ route('agreements.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Nouvelle Convention
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <table class="min-w-full bg-white border rounded shadow">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left">Discount</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Description</th>
                <th class="px-4 py-2 text-left">Créée le</th>
                <th class="px-4 py-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agreements as $agreement)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $agreement['discount_value'] }}</td>
                    <td class="px-4 py-2">{{ ucfirst($agreement['discount_type']) }}</td>
                    <td class="px-4 py-2">{{ $agreement['description'] ?? '-' }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($agreement['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('agreements.show', $agreement['id']) }}" class="text-blue-600 hover:underline">Voir</a>
                        <a href="{{ route('agreements.edit', $agreement['id']) }}" class="text-yellow-600 hover:underline">Modifier</a>
                        <form action="{{ route('agreements.destroy', $agreement['id']) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette convention ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucune convention trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
