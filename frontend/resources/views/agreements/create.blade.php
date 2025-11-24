@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Nouvelle Convention</h1>

    <form action="{{ route('agreements.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Type de remise</label>
            <select name="discount_type" class="mt-1 block w-full border-gray-300 rounded" required>
                <option value="percentage">Pourcentage</option>
                <option value="fixed">Montant fixe</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Valeur de remise</label>
            <input type="number" step="0.01" name="discount_value" class="mt-1 block w-full border-gray-300 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Statut</label>
            <select name="status" class="mt-1 block w-full border-gray-300 rounded" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded"></textarea>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Enregistrer
            </button>
            <a href="{{ route('agreements.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
