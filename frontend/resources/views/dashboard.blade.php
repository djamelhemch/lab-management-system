@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Patients --}}
    <a href="{{ route('patients.index') }}" class="block">
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 duration-300 border-l-8 border-blue-500">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Patients</p>
                    <p class="text-3xl font-extrabold text-blue-700">{{ $patientsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center shadow-inner">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-blue-50 text-blue-700 text-sm py-2 px-4">
                <span class="font-medium">Updated:</span> Just now
            </div>
        </div>
    </a>

    {{-- Doctors --}}
    <a href="{{ route('doctors.index') }}" class="block">
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 duration-300 border-l-8 border-green-500">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Doctors</p>
                    <p class="text-3xl font-extrabold text-green-700">{{ $doctorsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center shadow-inner">
                    <i class="fas fa-user-md text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-green-50 text-green-700 text-sm py-2 px-4">
                <span class="font-medium">Updated:</span> Just now
            </div>
        </div>
    </a>

    {{-- Samples Today --}}
    <a href="{{ route('samples.index') }}" class="block">
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 duration-300 border-l-8 border-yellow-500">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Samples Today</p>
                    <p class="text-3xl font-extrabold text-yellow-700">{{ $samplesToday }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center shadow-inner">
                    <i class="fas fa-vial text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-yellow-50 text-yellow-700 text-sm py-2 px-4">
                <span class="font-medium">Updated:</span> Just now
            </div>
        </div>
    </a>

    {{-- Pending Reports --}}
    <a href="{{ route('reports.index') }}" class="block">
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 duration-300 border-l-8 border-red-500">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending Reports</p>
                    <p class="text-3xl font-extrabold text-red-700">{{ $pendingReports }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center shadow-inner">
                    <i class="fas fa-file-medical text-red-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-red-50 text-red-700 text-sm py-2 px-4">
                <span class="font-medium">Updated:</span> Just now
            </div>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Activity Feed -->
    <div class="bg-white rounded-xl shadow p-6 col-span-1">
        <h2 class="text-lg font-bold mb-4 text-gray-700 flex items-center"><i class="fas fa-bolt text-yellow-400 mr-2"></i> Recent Activity</h2>
        <ul class="space-y-4">
            @foreach($recentActivities as $activity)
                <li class="flex items-start">
                    <span class="w-2 h-2 mt-2 rounded-full bg-{{ $activity['color'] ?? 'gray' }}-400 mr-3"></span>
                    <div>
                        <p class="text-sm text-gray-800">{{ $activity['description'] }}</p>
                        <span class="text-xs text-gray-400">{{ $activity['time'] }}</span>
                    </div>
                </li>
            @endforeach
            @if(empty($recentActivities))
                <li class="text-gray-400 text-sm">No recent activity.</li>
            @endif
        </ul>
    </div>

    <!-- Recent Patients Table -->
    <div class="bg-white rounded-xl shadow p-6 col-span-2 overflow-x-auto">
        <h2 class="text-lg font-bold mb-4 text-gray-700 flex items-center"><i class="fas fa-user-injured text-blue-400 mr-2"></i> Recent Patients</h2>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-gray-600 bg-gray-50">
                    <th class="px-4 py-2 text-left">Dossier N°</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Doctor</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPatients as $p)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 font-semibold">{{ $p['file_number'] }}</td>
                        <td class="px-4 py-2">{{ $p['first_name'] }} {{ $p['last_name'] }}</td>
                        <td class="px-4 py-2">{{ $p['doctor_name'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-bold
                                {{ $p['status'] === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                @if(empty($recentPatients))
                    <tr><td colspan="4" class="text-center text-gray-400 py-4">No recent patients.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white shadow rounded-xl p-8 mt-8">
    <h2 class="text-lg font-semibold mb-6 text-gray-700">Quick Actions</h2>
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('patients.create') }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Add Patient
        </a>
        <a href="{{ route('samples.create') }}" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow flex items-center">
            <i class="fas fa-vial mr-2"></i> Add Sample
        </a>
        <a href="{{ route('reports.index') }}" class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow flex items-center">
            <i class="fas fa-file-medical mr-2"></i> View Reports
        </a>
        <a href="{{ route('analyses.create') }}" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium shadow flex items-center">  
            <i class="fas fa-flask mr-2"></i> Add Analysis  
        </a>
    </div>
</div>
@endsection