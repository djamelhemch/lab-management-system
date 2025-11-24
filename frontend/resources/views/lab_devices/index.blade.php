@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Lab Devices</h1>
                </div>
                <a href="{{ route('lab-devices.create') }}" 
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-lg font-medium transition-all hover:shadow-xl hover:scale-105 w-full sm:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Device
                </a>
            </div>

            <!-- Toast Container -->
            <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"></div>

            <!-- Content -->
            <div class="p-4 sm:p-6">
                @if(empty($devices))
                    <div class="text-center py-16">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500 text-lg">No lab devices found</p>
                        <p class="text-gray-400 text-sm mt-2">Add your first device to get started</p>
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block">
                        <div id="device-table-wrapper" class="rounded-lg border border-gray-200 overflow-hidden">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manufacturer</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Network</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @include('lab_devices.partials.device_table_rows')
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile/Tablet Card View -->
                    <div class="lg:hidden space-y-4">
                        @foreach ($devices as $device)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden" data-device-id="{{ $device['id'] }}">
                                <!-- Card Header -->
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="w-3 h-3 rounded-full {{ $device['status'] === 'online' ? 'bg-green-500 animate-pulse' : ($device['status'] === 'offline' ? 'bg-gray-400' : ($device['status'] === 'error' ? 'bg-red-500' : 'bg-yellow-500')) }}"></span>
                                            <h3 class="font-semibold text-gray-900">{{ $device['name'] }}</h3>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $device['status'] === 'online' ? 'bg-green-100 text-green-800' : ($device['status'] === 'offline' ? 'bg-gray-100 text-gray-600' : ($device['status'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                            <span class="w-2 h-2 rounded-full {{ $device['status'] === 'online' ? 'bg-green-500 animate-pulse' : ($device['status'] === 'offline' ? 'bg-gray-400' : ($device['status'] === 'error' ? 'bg-red-500' : 'bg-yellow-500')) }}"></span>
                                            {{ ucfirst($device['status'] ?? 'unknown') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="px-4 py-3 space-y-2">
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-500">Manufacturer:</span>
                                            <p class="font-medium text-gray-900">{{ $device['manufacturer'] ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Model:</span>
                                            <p class="font-medium text-gray-900">{{ $device['model'] ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Type:</span>
                                            <p class="font-medium text-gray-900 capitalize">{{ $device['device_type'] ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Network:</span>
                                            <p class="font-mono text-xs font-medium text-gray-900">
                                                {{ $device['ip_address'] ?? '-' }}{{ $device['tcp_port'] ? ':' . $device['tcp_port'] : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Actions -->
                                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('lab-devices.edit', $device['id']) }}" 
                                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-yellow-500 hover:bg-yellow-600 rounded-lg text-white text-xs font-medium transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <button onclick="testConnection({{ $device['id'] }}, '{{ addslashes($device['name']) }}')" 
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white text-xs font-medium transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Test
                                        </button>
                                        <form action="{{ route('lab-devices.destroy', $device['id']) }}" method="POST" 
                                              class="flex-1" onsubmit="return confirm('Delete {{ addslashes($device['name']) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-white text-xs font-medium transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const showToast = (message, type = 'success', title = '') => {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };
    
    const icons = {
        success: '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
    };
    
    toast.className = `flex items-start gap-3 p-4 rounded-lg border shadow-lg ${colors[type]} transform transition-all duration-300 ease-out`;
    toast.innerHTML = `
        ${icons[type]}
        <div class="flex-1 min-w-0">
            ${title ? `<p class="font-semibold truncate">${title}</p>` : ''}
            <p class="text-sm">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    `;
    
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
};

window.testConnection = function(deviceId, deviceName) {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Testing...</span>
    `;
    
    fetch(`${window.apiUrl}/lab-devices/${deviceId}/test_connection`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success', deviceName);
        } else if (data.status === 'error') {
            showToast(data.message, 'error', deviceName);
        } else {
            showToast(data.message, 'info', deviceName);
        }
        
        // Refresh to update status
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        showToast('Failed to test connection', 'error', 'Network Error');
        console.error(err);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalContent;
    });
};

// Auto-refresh every 15 seconds (desktop only)
if (window.innerWidth >= 1024) {
    setInterval(() => {
        fetch('{{ route("lab-devices.index") }}?ajax=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const tbody = document.querySelector('#device-table-wrapper tbody');
            if (tbody) tbody.innerHTML = html;
        })
        .catch(err => console.error('Auto-refresh failed:', err));
    }, 15000);
}
</script>
@endsection

