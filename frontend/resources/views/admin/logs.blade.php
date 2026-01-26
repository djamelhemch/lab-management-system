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
                                <option value="{{ $user['id'] ?? $user['user_id'] }}" {{ request('user_id') == ($user['id'] ?? $user['user_id']) ? 'selected' : '' }}>
                                    {{ $user['full_name'] ?? $user['name'] ?? 'Unknown User' }}
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
                            <option value="create_patient" {{ request('action_type') == 'create_patient' ? 'selected' : '' }}>👤 Patient Created</option>
                            <option value="create_quotation" {{ request('action_type') == 'create_quotation' ? 'selected' : '' }}>📄 Quotation Created</option>
                            <option value="create_analysis" {{ request('action_type') == 'create_analysis' ? 'selected' : '' }}>🔬 Analysis Created</option>
                            <option value="login" {{ request('action_type') == 'login' ? 'selected' : '' }}>🔑 User Login</option>
                            <option value="logout" {{ request('action_type') == 'logout' ? 'selected' : '' }}>🚪 User Logout</option>
                            <option value="create_patient_failed" {{ request('action_type') == 'create_patient_failed' ? 'selected' : '' }}>❌ Patient Create Failed</option>
                            <option value="create_quotation_failed" {{ request('action_type') == 'create_quotation_failed' ? 'selected' : '' }}>❌ Quotation Create Failed</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Date From</label>
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
                        <span class="text-sm text-gray-500">{{ $pagination['total'] }} total entries</span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Device</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="logs-table-body">
                        @include('admin.partials.logs_rows', ['logs' => $logs])
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

@section('scripts')
<script>
function refreshLogs() {
    const params = new URLSearchParams(window.location.search);

    fetch("{{ route('admin.logs.partial') }}?" + params.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.text();
    })
    .then(html => {
        document.getElementById('logs-table-body').innerHTML = html;
    })
    .catch(err => {
        console.error("Log refresh failed:", err);
    });
}

// Refresh every 10 seconds
setInterval(refreshLogs, 10000);

// Also load on page load
document.addEventListener('DOMContentLoaded', refreshLogs);
</script>
