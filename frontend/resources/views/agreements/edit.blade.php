@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-10">
    <div class="max-w-2xl mx-auto px-6">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('agreements.index') }}"
               class="p-2 rounded-lg hover:bg-gray-200 transition">
                ←
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Modifier Convention #{{ $agreement['id'] }}
                </h1>
                <p class="text-gray-500 text-sm">Mettre à jour les paramètres de remise</p>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200">
            <div class="px-8 py-6 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Paramètres de la convention</h2>
            </div>

            <form action="{{ route('agreements.update', $agreement['id']) }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                {{-- Discount Type --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Type de remise
                    </label>
                    <select name="discount_type"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622]">
                        <option value="percentage" @selected($agreement['discount_type']=='percentage')>Pourcentage (%)</option>
                        <option value="fixed" @selected($agreement['discount_type']=='fixed')>Montant fixe (€)</option>
                    </select>
                </div>

                {{-- Value --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Valeur
                    </label>
                    <input type="number"
                           name="discount_value"
                           value="{{ $agreement['discount_value'] }}"
                           step="0.01"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622]">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Statut
                    </label>
                    <select name="status"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]">
                        <option value="active" @selected($agreement['status']=='active')>Active</option>
                        <option value="inactive" @selected($agreement['status']=='inactive')>Inactive</option>
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-[#bc1622] focus:border-[#bc1622]">{{ $agreement['description'] }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit"
                        class="flex-1 bg-[#bc1622] text-white py-3 rounded-lg font-semibold shadow-md hover:opacity-90 transition">
                        Mettre à jour
                    </button>

                    <a href="{{ route('agreements.index') }}"
                       class="flex-1 text-center border border-gray-300 py-3 rounded-lg font-semibold text-gray-700 hover:bg-gray-100 transition">
                        Annuler
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
