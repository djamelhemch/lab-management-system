@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Activity Logs</h1>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex space-x-4 mb-6">
        <select name="user_id" class="border rounded px-3 py-2">
            <option value="">All Users</option>
            @foreach($users as $user)
                <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                    {{ $user['full_name'] }}
                </option>
            @endforeach
        </select>

        <select name="action_type" class="border rounded px-3 py-2">
            <option value="">All Actions</option>
            <option value="create_patient" {{ request('action_type') == 'create_patient' ? 'selected' : '' }}>Patient</option>
            <option value="create_quotation" {{ request('action_type') == 'create_quotation' ? 'selected' : '' }}>Quotation</option>
            <option value="create_analysis" {{ request('action_type') == 'create_analysis' ? 'selected' : '' }}>Analysis</option>
            <option value="login" {{ request('action_type') == 'login' ? 'selected' : '' }}>Login</option>
            <option value="logout" {{ request('action_type') == 'logout' ? 'selected' : '' }}>Logout</option>
        </select>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Filter</button>
    </form>

    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log['user_name'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log['action_type'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log['description'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log['ip_address'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log['user_agent'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log['created_at'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
                @if(isset($pagination))
            <div class="mt-4 flex justify-between items-center">
                <div>Showing page {{ $pagination['page'] }} of {{ $pagination['last_page'] }} (Total: {{ $pagination['total'] }})</div>
                <div class="space-x-2">
                    {{-- Previous Button --}}
                    @if($pagination['page'] > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['page'] - 1]) }}"
                        class="px-3 py-1 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300">
                            Previous
                        </a>
                    @else
                        <span class="px-3 py-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">Previous</span>
                    @endif

                    {{-- Next Button --}}
                    @if($pagination['page'] < $pagination['last_page'])
                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['page'] + 1]) }}"
                        class="px-3 py-1 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300">
                            Next
                        </a>
                    @else
                        <span class="px-3 py-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
            @endif
</div>
@endsection
