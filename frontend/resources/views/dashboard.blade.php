@extends('layouts.app')

@section('title', 'Hub - Abdelatif Lab')

@section('content')

{{-- Welcome Header --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">
        Bienvenue, {{ session('user')['username'] ?? 'Utilisateur' }} 👋
    </h1>
    <p class="text-gray-600">Accès rapide à vos outils de travail quotidiens</p>
</div>

{{-- Quick Financial Summary Banner --}}
<div class="mb-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6 border border-green-200">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Revenus Aujourd'hui</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($todayRevenue, 0) }} DA</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Revenus Ce Mois</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($monthRevenue, 0) }} DA</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Impayés Total</p>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($outstandingTotal, 0) }} DA</p>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions Grid --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-bolt text-yellow-500"></i> Actions rapides
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- New Patient --}}
        <a href="{{ route('patients.create') }}" 
           class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-user-plus text-3xl opacity-90"></i>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <h3 class="font-bold text-lg">Nouveau Patient</h3>
            <p class="text-sm opacity-90 mt-1">Enregistrer un patient</p>
        </a>

        {{-- New Visit/Quotation --}}
        <a href="{{ route('quotations.create') }}" 
           class="group bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-file-medical text-3xl opacity-90"></i>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <h3 class="font-bold text-lg">Nouvelle Visite</h3>
            <p class="text-sm opacity-90 mt-1">Créer un devis</p>
        </a>

        {{-- Samples --}}
        <a href="{{ route('samples.index') }}" 
           class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-vial text-3xl opacity-90"></i>
                <span class="bg-white text-purple-600 text-xs font-bold px-2 py-1 rounded-full">{{ $samplesToday }}</span>
            </div>
            <h3 class="font-bold text-lg">Échantillons</h3>
            <p class="text-sm opacity-90 mt-1">{{ $samplesToday }} aujourd'hui</p>
        </a>

        {{-- Results --}}
        <a href="{{ route('lab-results.index') }}" 
           class="group bg-gradient-to-br from-[#ff6b6b] to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-vials text-3xl opacity-90"></i>
                <span class="bg-white text-red-600 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingReports }}</span>
            </div>
            <h3 class="font-bold text-lg">Résultats</h3>
            <p class="text-sm opacity-90 mt-1">{{ $pendingReports }} en attente</p>
        </a>
    </div>
</section>

{{-- Work Areas --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-briefcase text-gray-600"></i> Espaces de travail
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Reception Queue --}}
        <a href="{{ route('queues.index', ['type' => 'reception']) }}" 
           class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all border-l-4 border-indigo-500 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-concierge-bell text-indigo-600 text-xl"></i>
                </div>
                <span class="bg-indigo-100 text-indigo-700 text-sm font-bold px-3 py-1 rounded-full">
                    {{ $receptionQueueCount ?? 0 }}
                </span>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Réception</h3>
            <p class="text-sm text-gray-600">Patients en attente d'accueil</p>
        </a>

        {{-- Blood Draw Queue --}}
        <a href="{{ route('queues.index', ['type' => 'blood_draw']) }}" 
           class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all border-l-4 border-pink-500 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-syringe text-pink-600 text-xl"></i>
                </div>
                <span class="bg-pink-100 text-pink-700 text-sm font-bold px-3 py-1 rounded-full">
                    {{ $bloodDrawQueueCount ?? 0 }}
                </span>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Prélèvement Sanguin</h3>
            <p class="text-sm text-gray-600">Patients à prélever</p>
        </a>

        {{-- Waiting Room Display --}}
        <a href="{{ route('queues.show') }}" 
           class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all border-l-4 border-teal-500 group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-tv text-teal-600 text-xl"></i>
                </div>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Salle d'attente</h3>
            <p class="text-sm text-gray-600">Affichage des numéros</p>
        </a>
    </div>
</section>

{{-- Management Section --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-cog text-gray-600"></i> Gestion
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        
        {{-- Patients --}}
        <a href="{{ route('patients.index') }}" 
           class="bg-white rounded-xl p-4 shadow hover:shadow-lg transition-all text-center group">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-user-injured text-blue-600 text-xl"></i>
            </div>
            <p class="text-xs font-semibold text-gray-700">Patients</p>
            <p class="text-lg font-bold text-blue-600">{{ $patientsCount }}</p>
        </a>

        {{-- Doctors --}}
        <a href="{{ route('doctors.index') }}" 
           class="bg-white rounded-xl p-4 shadow hover:shadow-lg transition-all text-center group">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-user-md text-green-600 text-xl"></i>
            </div>
            <p class="text-xs font-semibold text-gray-700">Médecins</p>
            <p class="text-lg font-bold text-green-600">{{ $doctorsCount }}</p>
        </a>

        {{-- Analyses --}}
        <a href="{{ route('analyses.index') }}" 
           class="bg-white rounded-xl p-4 shadow hover:shadow-lg transition-all text-center group">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-flask text-purple-600 text-xl"></i>
            </div>
            <p class="text-xs font-semibold text-gray-700">Analyses</p>
        </a>

        {{-- Lab Devices --}}
        <a href="{{ route('lab-devices.index') }}" 
           class="bg-white rounded-xl p-4 shadow hover:shadow-lg transition-all text-center group">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-microscope text-orange-600 text-xl"></i>
            </div>
            <p class="text-xs font-semibold text-gray-700">Appareils</p>
        </a>

        {{-- Visits/Quotations --}}
        <a href="{{ route('quotations.index') }}" 
           class="bg-white rounded-xl p-4 shadow hover:shadow-lg transition-all text-center group">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-invoice text-yellow-600 text-xl"></i>
            </div>
            <p class="text-xs font-semibold text-gray-700">Visites</p>
            <p class="text-lg font-bold text-yellow-600">{{ $quotationsCount ?? 0 }}</p>
        </a>

        {{-- Statistics Link --}}
        <a href="{{ route('statistics.index') }}" 
           class="bg-gradient-to-br from-gray-700 to-gray-800 text-white rounded-xl p-4 shadow hover:shadow-lg transition-all text-center group">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <p class="text-xs font-semibold">Statistiques</p>
        </a>
    </div>
</section>

{{-- Activity Timeline and Recent Patients --}}
<section>
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-history text-gray-600"></i> Activité en temps réel
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Activity Timeline (2/3 width) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-stream text-yellow-500"></i> Flux d'activités
            </h3>
            <div class="space-y-2 max-h-[500px] overflow-y-auto">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                        <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 font-medium">{{ $activity['description'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-500">{{ $activity['time'] }}</span>
                                @if(isset($activity['user']))
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">{{ $activity['user'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-8">Aucune activité récente</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Patients (1/3 width) --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-blue-500"></i> Nouveaux patients
            </h3>
            <div class="space-y-3 max-h-[500px] overflow-y-auto">
                @forelse($recentPatients->take(8) as $p)
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $p['first_name'] }} {{ $p['last_name'] }}</p>
                            <p class="text-xs text-gray-500">N° {{ $p['file_number'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-8">Aucun patient récent</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setInterval(() => {
        window.location.reload();
    }, 30000);
</script>
@endpush
