@extends('layouts.app')

@section('title', 'Hub de Travail - Abdelatif Lab')

@section('content')

<div class="max-w-6xl mx-auto py-10 px-6">

    {{-- Header --}}
    <div class="mb-12 text-center">
       <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center justify-center gap-3 text-center">
            Espace de travail rapide
            <span class="text-sm font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-md">
                Version en cours de développement
            </span>
        </h1>
        <p class="text-gray-600">
            Accédez directement aux actions principales du laboratoire
        </p>
    </div>

    {{-- MAIN HUB ACTIONS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        {{-- 🧪 ANALYSES --}}
        <a href="{{ route('analyses.index') }}"
           class="group relative bg-gradient-to-br from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800
                  text-white rounded-3xl p-10 shadow-xl hover:shadow-2xl
                  transition-all duration-300 transform hover:-translate-y-2">

            <div class="absolute top-6 right-6 opacity-20 text-6xl">
                <i class="fas fa-flask"></i>
            </div>

            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-vial text-3xl"></i>
                </div>

                <h2 class="text-2xl font-bold mb-2">Analyses</h2>
                <p class="opacity-90 text-sm">
                    Gérer les examens de laboratoire et leurs paramètres
                </p>
            </div>
        </a>

        {{-- 🧾 QUOTATIONS / VISITS --}}
        <a href="{{ route('quotations.create') }}"
           class="group relative bg-gradient-to-br from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800
                  text-white rounded-3xl p-10 shadow-xl hover:shadow-2xl
                  transition-all duration-300 transform hover:-translate-y-2">

            <div class="absolute top-6 right-6 opacity-20 text-6xl">
                <i class="fas fa-file-medical"></i>
            </div>

            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-plus-circle text-3xl"></i>
                </div>

                <h2 class="text-2xl font-bold mb-2">Nouveau Devis / Visite</h2>
                <p class="opacity-90 text-sm">
                    Créer une visite patient et sélectionner les analyses
                </p>
            </div>
        </a>

        {{-- 📊 RESULTS --}}
        <a href="{{ route('lab-results.index') }}"
           class="group relative bg-gradient-to-br from-[#bc1622] to-red-700 hover:from-red-700 hover:to-red-800
                  text-white rounded-3xl p-10 shadow-xl hover:shadow-2xl
                  transition-all duration-300 transform hover:-translate-y-2">

            <div class="absolute top-6 right-6 opacity-20 text-6xl">
                <i class="fas fa-vials"></i>
            </div>

            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-notes-medical text-3xl"></i>
                </div>

                <h2 class="text-2xl font-bold mb-2">Résultats</h2>
                <p class="opacity-90 text-sm">
                    Saisir, valider et imprimer les résultats d'analyses
                </p>
            </div>
        </a>

    </div>

    {{-- Secondary subtle navigation --}}
    <div class="mt-14 text-center text-sm text-gray-500">
        <p>Autres fonctionnalités disponibles via le menu principal</p>
    </div>

</div>

@endsection
