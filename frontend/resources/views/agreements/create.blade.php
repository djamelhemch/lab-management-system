@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-10">
    <div class="max-w-3xl mx-auto px-6">

        <!-- PAGE HEADER -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('agreements.index') }}"
                   class="p-2 rounded-lg hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Nouvelle Convention</h1>
                    <p class="text-gray-500 text-sm">Configurer une règle de remise</p>
                </div>
            </div>

            <span class="bg-red-100 text-[#bc1622] text-xs font-semibold px-3 py-1 rounded-full">
                Paramètres financiers
            </span>
        </div>

        <!-- CARD -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200">

            <div class="px-8 py-6 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Détails de la convention</h2>
            </div>

            <form action="{{ route('agreements.store') }}" method="POST" class="p-8 space-y-7">
                @csrf

                <!-- TYPE -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Type de remise <span class="text-red-500">*</span>
                    </label>
                    <select id="discount_type" name="discount_type"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622] transition">
                        <option value="percentage">Pourcentage (%)</option>
                        <option value="fixed">Montant fixe (DA)</option>
                    </select>
                </div>

                <!-- VALUE -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Valeur de remise <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="discount_value" name="discount_value"
                               step="0.01" min="0"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622] transition pr-16"
                               placeholder="Ex: 10">
                        <span id="unitLabel"
                              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                </div>

                <!-- STATUS -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622] transition">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- DESCRIPTION -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#bc1622] focus:border-[#bc1622] transition"
                        placeholder="Notes internes sur cette convention..."></textarea>
                </div>

                <!-- ACTIONS -->
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit"
                        class="flex-1 bg-[#bc1622] text-white py-3 rounded-lg font-semibold hover:opacity-90 transition shadow-md">
                        Enregistrer
                    </button>

                    <a href="{{ route('agreements.index') }}"
                       class="flex-1 text-center border border-gray-300 py-3 rounded-lg font-semibold text-gray-700 hover:bg-gray-100 transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

        <!-- HELP -->
        <div class="mt-6 bg-white border rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Conseils</h3>
            <ul class="text-xs text-gray-600 space-y-1">
                <li>• % doit être entre 0 et 100</li>
                <li>• Montant fixe = déduit directement du total</li>
                <li>• Les conventions actives sont utilisables immédiatement</li>
            </ul>
        </div>

    </div>
</div>

<!-- Smart UX Script -->
<script>
    const typeSelect = document.getElementById('discount_type');
    const unitLabel = document.getElementById('unitLabel');

    function updateUnit() {
        unitLabel.textContent = typeSelect.value === 'percentage' ? '%' : 'DA';
    }
    typeSelect.addEventListener('change', updateUnit);
    updateUnit();
</script>
@endsection
