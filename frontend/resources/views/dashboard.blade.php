@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Section: Statistics -->
<section class="mb-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        📊 Statistics
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @include('partials.dashboard-card', [
            'title' => 'Total Patients',
            'count' => $patientsCount,
            'color' => 'blue',
            'icon' => 'users',
            'route' => route('patients.index'),
            'updatedId' => 'patientsUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Doctors',
            'count' => $doctorsCount,
            'color' => 'green',
            'icon' => 'user-md',
            'route' => route('doctors.index'),
            'updatedId' => 'doctorsUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Samples Today',
            'count' => $samplesToday,
            'color' => 'yellow',
            'icon' => 'vial',
            'route' => route('samples.index'),
            'updatedId' => 'samplesUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Pending Reports',
            'count' => $pendingReports,
            'color' => 'red',
            'icon' => 'file-medical',
            'route' => route('reports.index'),
            'updatedId' => 'pendingReportsUpdated'
        ])
    </div>
</section>

<!-- Section: Queues -->
<section class="mb-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        🕐 Queues
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @include('partials.dashboard-card', [
            'title' => 'Reception Queue',
            'count' => $receptionQueueCount ?? 0,
            'color' => 'purple',
            'icon' => 'concierge-bell',
            'route' => route('queues.index', ['type' => 'reception']),
            'updatedId' => 'receptionQueueUpdated'
        ])
        @include('partials.dashboard-card', [
            'title' => 'Blood Draw Queue',
            'count' => $bloodDrawQueueCount ?? 0,
            'color' => 'pink',
            'icon' => 'syringe',
            'route' => route('queues.index', ['type' => 'blood_draw']),
            'updatedId' => 'bloodDrawQueueUpdated'
        ])
    </div>
</section>

<!-- Section: Quotations -->
<section class="mb-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        📄 Quotations
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @include('partials.dashboard-card', [
            'title' => 'Total Quotations',
            'count' => $quotationsCount ?? 0,
            'color' => 'indigo',
            'icon' => 'file-invoice-dollar',
            'route' => route('quotations.index'),
            'updatedId' => 'quotationsUpdated'
        ])
    </div>
</section>

<!-- Section: Recent Activity and Patients -->
<section>
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        📋 Activity & Patients
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Activity Feed -->
        <div class="bg-white rounded-xl shadow p-6 col-span-1">
            <h3 class="text-lg font-bold mb-4 text-gray-700 flex items-center">
                <i class="fas fa-bolt text-yellow-400 mr-2"></i> Recent Activity
            </h3>
            <ul class="space-y-4">
                @forelse($recentActivities as $activity)
                    <li class="flex items-start">
                        <span class="w-2 h-2 mt-2 rounded-full bg-{{ $activity['color'] ?? 'gray' }}-400 mr-3"></span>
                        <div>
                            <p class="text-sm text-gray-800">{{ $activity['description'] }}</p>
                            <span class="text-xs text-gray-500">{{ $activity['time'] }}</span>
                        </div>
                    </li>
                @empty
                    <li class="text-gray-400 text-sm">No recent activity.</li>
                @endforelse
            </ul>
        </div>

        <!-- Recent Patients Table -->
        <div class="bg-white rounded-xl shadow p-6 col-span-2 overflow-x-auto">
            <h3 class="text-lg font-bold mb-4 text-gray-700 flex items-center">
                <i class="fas fa-user-injured text-blue-400 mr-2"></i> Recent Patients
            </h3>
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-gray-600 bg-gray-50">
                        <th class="px-4 py-2 text-left">Dossier N°</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Doctor</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPatients as $p)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2 font-semibold text-gray-800">{{ $p['file_number'] }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $p['first_name'] }} {{ $p['last_name'] }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $p['doctor_name'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-bold
                                    {{ $p['status'] === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $p['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-400 py-4">No recent patients.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
@push('scripts')
<script>
    async function refreshDashboardCards() {
        try {
            const res = await fetch('{{ route('dashboard.stats') }}');
            const stats = await res.json();

            const map = {
                patientsUpdated: stats.patientsCount,
                doctorsUpdated: stats.doctorsCount,
                samplesUpdated: stats.samplesToday,
                pendingReportsUpdated: stats.pendingReports,
                receptionQueueUpdated: stats.receptionQueueCount,
                bloodDrawQueueUpdated: stats.bloodDrawQueueCount,
                quotationsUpdated: stats.quotationsCount
            };

            for (const [id, value] of Object.entries(map)) {
                const updatedEl = document.getElementById(id);
                if (!updatedEl) continue;
                const card = updatedEl.closest('a');
                const countEl = card?.querySelector('p.text-2xl');
                if (countEl) countEl.textContent = value;
                updatedEl.textContent = 'Updated at ' + new Date().toLocaleTimeString();
            }
        } catch (e) {
            console.error("Failed to fetch stats:", e);
        }
    }

    // Run every 10 seconds
    setInterval(refreshDashboardCards, 10000);
</script>
@endpush
