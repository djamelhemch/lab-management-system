@extends('layouts.app')

@section('title', 'Statistiques Avancées - Abdelatif Lab')

@section('content')

{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
        <i class="fas fa-chart-line text-[#ff6b6b]"></i> Statistiques & Analyses Prédictives
    </h1>
    <p class="text-gray-600">Analyses financières détaillées et prévisions intelligentes</p>
</div>

{{-- Predictive Analytics Section --}}
@if(isset($predictions) && !empty($predictions))
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-brain text-purple-600"></i> Analyses Prédictives
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Revenue Forecast --}}
        @if(isset($predictions['revenue_forecast']))
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-chart-line text-3xl opacity-80"></i>
                @if($predictions['revenue_forecast']['trend'] === 'increasing')
                    <i class="fas fa-arrow-up text-2xl"></i>
                @else
                    <i class="fas fa-arrow-down text-2xl"></i>
                @endif
            </div>
            <p class="text-sm opacity-90 font-medium">Prévision Semaine Prochaine</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($predictions['revenue_forecast']['predicted_next_week'], 0) }} DA</p>
            <p class="text-xs mt-2 opacity-75">
                Tendance: {{ $predictions['revenue_forecast']['trend'] === 'increasing' ? '📈 Croissance' : '📉 Baisse' }}
            </p>
        </div>
        @endif

        {{-- Patient Growth --}}
        @if(isset($predictions['patient_growth']))
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-users text-3xl opacity-80"></i>
                <i class="fas fa-chart-area text-2xl"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Nouveaux Patients Prévus</p>
            <p class="text-3xl font-bold mt-2">{{ $predictions['patient_growth']['predicted_next_month'] }}</p>
            <p class="text-xs mt-2 opacity-75">
                Croissance: {{ number_format($predictions['patient_growth']['growth_rate'], 1) }}%
            </p>
        </div>
        @endif

        {{-- Sample Volume --}}
        @if(isset($predictions['sample_metrics']))
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-vial text-3xl opacity-80"></i>
                <i class="fas fa-calendar-day text-2xl"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Échantillons Demain</p>
            <p class="text-3xl font-bold mt-2">{{ $predictions['sample_metrics']['predicted_tomorrow'] }}</p>
            <p class="text-xs mt-2 opacity-75">
                Moyenne: {{ number_format($predictions['sample_metrics']['avg_daily_samples'], 1) }}/jour
            </p>
        </div>
        @endif

        {{-- Financial Health Score --}}
        @if(isset($predictions['financial_health']))
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-heartbeat text-3xl opacity-80"></i>
                <div class="text-right">
                    <div class="text-4xl font-bold">{{ round($predictions['financial_health']['health_score']) }}</div>
                    <div class="text-xs opacity-75">/100</div>
                </div>
            </div>
            <p class="text-sm opacity-90 font-medium">Score de Santé Financière</p>
            <div class="mt-3 bg-white bg-opacity-20 rounded-full h-2">
                <div class="bg-white rounded-full h-2 transition-all" style="width: {{ $predictions['financial_health']['health_score'] }}%"></div>
            </div>
            <p class="text-xs mt-2 opacity-75">
                Taux recouvrement: {{ number_format($predictions['financial_health']['collection_rate'], 1) }}%
            </p>
        </div>
        @endif
    </div>
</section>
@endif

{{-- Revenue at Risk Alert --}}
@if(isset($predictions['financial_health']['at_risk_revenue']) && $predictions['financial_health']['at_risk_revenue'] > 0)
<div class="mb-10 bg-red-50 border-l-4 border-red-500 rounded-lg p-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-red-900 mb-1">⚠️ Revenus à Risque Détectés</h3>
            <p class="text-red-700 text-sm mb-2">
                Des paiements en attente depuis plus de 30 jours nécessitent une attention immédiate.
            </p>
            <p class="text-2xl font-bold text-red-600">
                {{ number_format($predictions['financial_health']['at_risk_revenue'], 2) }} DA
            </p>
        </div>
        <a href="{{ route('quotations.index') }}?status=converted" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            Voir les Visites
        </a>
    </div>
</div>
@endif

{{-- Revenue Overview (Today, Week, Month, Year) --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-money-bill-wave text-green-600"></i> Revenus Encaissés
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        {{-- Today --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-5 shadow-lg hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-calendar-day text-2xl opacity-80"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Aujourd'hui</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($paidToday, 2) }} DA</p>
        </div>

        {{-- Week --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-5 shadow-lg hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-calendar-week text-2xl opacity-80"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Cette Semaine</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($paidWeek, 2) }} DA</p>
        </div>

        {{-- Month --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-5 shadow-lg hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-calendar-alt text-2xl opacity-80"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Ce Mois</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($paidMonth, 2) }} DA</p>
        </div>

        {{-- Year --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-5 shadow-lg hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-calendar text-2xl opacity-80"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Cette Année</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($paidYear, 2) }} DA</p>
        </div>

        {{-- All Time --}}
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-xl p-5 shadow-lg hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-infinity text-2xl opacity-80"></i>
            </div>
            <p class="text-sm opacity-90 font-medium">Total Historique</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($paidAllTime, 2) }} DA</p>
        </div>
    </div>
</section>

{{-- Outstanding Balance --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-hourglass-half text-yellow-600"></i> Soldes Impayés
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        {{-- Today --}}
        <div class="bg-white border-2 border-yellow-200 rounded-xl p-5 shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-exclamation-circle text-yellow-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Aujourd'hui</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($outstandingToday, 2) }} DA</p>
        </div>

        {{-- Week --}}
        <div class="bg-white border-2 border-yellow-200 rounded-xl p-5 shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Cette Semaine</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($outstandingWeek, 2) }} DA</p>
        </div>

        {{-- Month --}}
        <div class="bg-white border-2 border-orange-200 rounded-xl p-5 shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-bell text-orange-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Ce Mois</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($outstandingMonth, 2) }} DA</p>
        </div>

        {{-- Year --}}
        <div class="bg-white border-2 border-red-200 rounded-xl p-5 shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Cette Année</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($outstandingYear, 2) }} DA</p>
        </div>

        {{-- All Time --}}
        <div class="bg-white border-2 border-red-300 rounded-xl p-5 shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-file-invoice-dollar text-red-600 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Total Impayé</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($outstandingAllTime, 2) }} DA</p>
        </div>
    </div>
</section>

{{-- Key Performance Indicators (KPIs) --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-tachometer-alt text-indigo-600"></i> Indicateurs de Performance (KPIs)
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        
        <div class="bg-white rounded-xl p-6 shadow-md border-l-4 border-indigo-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-list text-indigo-600 text-3xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Total Visites</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $totalQuotations }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md border-l-4 border-yellow-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-pause-circle text-yellow-600 text-3xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">En Attente</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingQuotations }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md border-l-4 border-green-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Converties</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $convertedQuotations }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md border-l-4 border-purple-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-percentage text-purple-600 text-3xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Taux Conversion</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ number_format($conversionRate, 1) }}%</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md border-l-4 border-pink-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-chart-bar text-pink-600 text-3xl"></i>
            </div>
            <p class="text-sm text-gray-600 font-medium">Valeur Moyenne</p>
            <p class="text-2xl font-bold text-pink-600 mt-1">{{ number_format($averageQuotationValue, 0) }} DA</p>
        </div>
    </div>
</section>

{{-- Revenue Trend Chart --}}
@if(isset($predictions['revenue_forecast']['historical_weekly']))
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-chart-area text-blue-600"></i> Tendance des Revenus (12 Semaines)
    </h2>
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="chart-container" style="position: relative; height: 350px;">
            <canvas id="revenueChart" width="400" height="350"></canvas>
        </div>
    </div>
</section>
@endif

{{-- Patient Growth Chart --}}
@if(isset($predictions['patient_growth']['historical_monthly']))
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-users text-green-600"></i> Croissance des Patients (6 Mois)
    </h2>
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="chart-container" style="position: relative; height: 350px;">
            <canvas id="patientChart" width="400" height="350"></canvas>
        </div>
    </div>
</section>
@endif
{{-- Operational Metrics --}}
<section class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Métriques Opérationnelles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @include('partials.dashboard-card', [
            'title' => 'Patients Totaux',
            'count' => $patientsCount,
            'color' => 'blue',
            'icon' => 'users',
            'route' => route('patients.index'),
            'updatedId' => 'patientsUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Médecins',
            'count' => $doctorsCount,
            'color' => 'green',
            'icon' => 'user-md',
            'route' => route('doctors.index'),
            'updatedId' => 'doctorsUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Échantillons Aujourd\'hui',
            'count' => $samplesToday,
            'color' => 'yellow',
            'icon' => 'vial',
            'route' => route('samples.index'),
            'updatedId' => 'samplesUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Résultats en Attente',
            'count' => $pendingReports,
            'color' => 'red',
            'icon' => 'file-medical',
            'route' => route('lab-results.index'),
            'updatedId' => 'pendingReportsUpdated'
        ])
    </div>
</section>

@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Revenue Trend Chart
@if(isset($predictions['revenue_forecast']['historical_weekly']))
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    const revenueData = @json($predictions['revenue_forecast']['historical_weekly']);
    const predictedRevenue = @json($predictions['revenue_forecast']['predicted_next_week']);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['S-11', 'S-10', 'S-9', 'S-8', 'S-7', 'S-6', 'S-5', 'S-4', 'S-3', 'S-2', 'S-1', 'Actuelle', 'Prévision'],
            datasets: [{
                label: 'Revenus (DA)',
                data: [...revenueData, predictedRevenue],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                segment: {
                    borderDash: ctx => ctx.p1DataIndex === 12 ? [5, 5] : undefined
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString('fr-DZ') + ' DA';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-DZ') + ' DA';
                        }
                    }
                }
            }
        }
    });
}
@endif

// Patient Growth Chart
@if(isset($predictions['patient_growth']['historical_monthly']))
const patientCtx = document.getElementById('patientChart');
if (patientCtx) {
    const patientData = @json($predictions['patient_growth']['historical_monthly']);
    const predictedPatients = @json($predictions['patient_growth']['predicted_next_month']);
    
    new Chart(patientCtx, {
        type: 'bar',
        data: {
            labels: ['M-5', 'M-4', 'M-3', 'M-2', 'M-1', 'Mois Actuel', 'Prévision'],
            datasets: [{
                label: 'Nouveaux Patients',
                data: [...patientData, predictedPatients],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(34, 197, 94, 0.4)'
                ],
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
@endif

// Auto-refresh every 5 minutes
setTimeout(() => {
    window.location.reload();
}, 300000);
</script>
@endpush
