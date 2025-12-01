@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Résultats du Laboratoire</h1>
            <p class="text-gray-600 mt-1">Gestion et consultation des analyses</p>
        </div>

        {{-- Flash messages --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- View Toggle Navigation --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-1 inline-flex">
            <a href="{{ route('lab-results.index', ['view' => 'chronological']) }}" 
               class="px-6 py-2.5 rounded-md font-medium text-sm transition-all {{ $view === 'chronological' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Tous les Résultats
                </span>
            </a>
            <a href="{{ route('lab-results.index', ['view' => 'patients']) }}" 
               class="px-6 py-2.5 rounded-md font-medium text-sm transition-all {{ $view === 'patients' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Par Patient
                </span>
            </a>
        </div>

        {{-- Dynamic Content Area --}}
        <div id="results-container">
            @if($view === 'chronological')
                @include('lab_results.partials.chronological', ['labResults' => $labResults])
            @elseif($view === 'patients')
                @include('lab_results.partials.patient_list', ['patients' => $patients])
            @endif
        </div>
    </div>
</div>
@endsection
