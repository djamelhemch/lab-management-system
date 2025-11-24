@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
                    <p class="mt-2 text-sm text-gray-600">Monitor user activities and system events in real-time</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Last updated: {{ now()->format('M d, Y H:i') }}
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 mb-8">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                    </svg>
                    Filters
                </h2>
                <p class="text-sm text-gray-500 mt-1">Refine your search to find specific activities</p>
            </div>
            
            <form method="GET" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    {{-- User Filter --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <select name="user_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                                    {{ $user['full_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Type Filter --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Action Type</label>
                        <select name="action_type" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                            <option value="">All Actions</option>
                            <option value="create_patient" {{ request('action_type') == 'create_patient' ? 'selected' : '' }}>
                                👤 Patient Created
                            </option>
                            <option value="create_quotation" {{ request('action_type') == 'create_quotation' ? 'selected' : '' }}>
                                📄 Quotation Created
                            </option>
                            <option value="create_analysis" {{ request('action_type') == 'create_analysis' ? 'selected' : '' }}>
                                🔬 Analysis Created
                            </option>
                            <option value="login" {{ request('action_type') == 'login' ? 'selected' : '' }}>
                                🔑 User Login
                            </option>
                            <option value="logout" {{ request('action_type') == 'logout' ? 'selected' : '' }}>
                                🚪 User Logout
                            </option>
                        </select>
                    </div>

                    {{-- Date Range (Future Enhancement) --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Date Range</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>

                    {{-- Filter Actions --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                        <div class="flex gap-3">
                            <button type="submit" 
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-sm">
                                Apply Filters
                            </button>
                            <a href="{{ url()->current() }}" 
                                class="px-4 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Activity Logs Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                    @if(isset($pagination))
                        <span class="text-sm text-gray-500">
                            {{ $pagination['total'] }} total entries
                        </span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Device
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Timestamp
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            {{-- User --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-indigo-800">
                                                {{ substr($log['user_name'] ?? 'N', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log['user_name'] ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Action Type with Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $actionColors = [
                                        'create_patient' => 'bg-green-100 text-green-800',
                                        'create_quotation' => 'bg-blue-100 text-blue-800',
                                        'create_analysis' => 'bg-purple-100 text-purple-800',
                                        'login' => 'bg-emerald-100 text-emerald-800',
                                        'logout' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $actionIcons = [
                                        'create_patient' => '👤',
                                        'create_quotation' => '📄',
                                        'create_analysis' => '🔬',
                                        'login' => '🔑',
                                        'logout' => '🚪',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $actionColors[$log['action_type']] ?? 'bg-gray-100 text-gray-800' }}">
                                    <span>{{ $actionIcons[$log['action_type']] ?? '📝' }}</span>
                                    {{ ucwords(str_replace('_', ' ', $log['action_type'])) }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $log['description'] }}">
                                    {{ $log['description'] }}
                                </div>
                            </td>

                            {{-- IP Address --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-600 bg-gray-50 px-2 py-1 rounded">
                                    {{ $log['ip_address'] ?? 'N/A' }}
                                </div>
                            </td>

                            {{-- User Agent (simplified) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500 max-w-32 truncate" title="{{ $log['user_agent'] ?? 'N/A' }}">
                                    @if($log['user_agent'] ?? false)
                                        @if(str_contains($log['user_agent'], 'Mobile') || str_contains($log['user_agent'], 'Android') || str_contains($log['user_agent'], 'iPhone'))
                                            📱 Mobile
                                        @elseif(str_contains($log['user_agent'], 'Chrome'))
                                            🌐 Chrome
                                        @elseif(str_contains($log['user_agent'], 'Firefox'))
                                            🦊 Firefox
                                        @elseif(str_contains($log['user_agent'], 'Safari'))
                                            🧭 Safari
                                        @else
                                            💻 Desktop
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>

                            {{-- Timestamp --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($log['created_at'])->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($log['created_at'])->format('H:i:s') }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No activity logs found</h3>
                                    <p class="text-gray-500">Try adjusting your filters or check back later.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Enhanced Pagination --}}
        @if(isset($pagination))
        <div class="mt-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing page <span class="font-medium">{{ $pagination['page'] }}</span> of 
                        <span class="font-medium">{{ $pagination['last_page'] }}</span>
                        (<span class="font-medium">{{ $pagination['total'] }}</span> total entries)
                    </div>
                    
                    <div class="flex items-center gap-2">
                        {{-- Previous Button --}}
                        @if($pagination['page'] > 1)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['page'] - 1]) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </span>
                        @endif

                        {{-- Next Button --}}
                        @if($pagination['page'] < $pagination['last_page'])
                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['page'] + 1]) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
