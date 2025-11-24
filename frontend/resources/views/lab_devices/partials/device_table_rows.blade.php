@foreach ($devices as $device)
<tr class="hover:bg-gray-50 transition-colors duration-150" data-device-id="{{ $device['id'] }}">
    <td class="px-4 py-3 whitespace-nowrap">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full flex-shrink-0 {{ $device['status'] === 'online' ? 'bg-green-500 animate-pulse' : ($device['status'] === 'offline' ? 'bg-gray-400' : ($device['status'] === 'error' ? 'bg-red-500' : 'bg-yellow-500')) }}"></span>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $device['name'] }}</div>
                <div class="text-xs text-gray-500">ID: {{ $device['id'] }}</div>
            </div>
        </div>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $device['manufacturer'] ?? '-' }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $device['model'] ?? '-' }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm">
        @if($device['ip_address'])
            <div class="flex items-center gap-1.5 text-gray-700">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                <span class="font-mono text-xs">{{ $device['ip_address'] }}:{{ $device['tcp_port'] ?? '-' }}</span>
            </div>
        @else
            <span class="text-gray-400">-</span>
        @endif
    </td>
    <td class="px-4 py-3 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 capitalize">
            {{ $device['device_type'] ?? 'unknown' }}
        </span>
    </td>
    <td class="px-4 py-3 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $device['status'] === 'online' ? 'bg-green-100 text-green-800' : ($device['status'] === 'offline' ? 'bg-gray-100 text-gray-600' : ($device['status'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
            <span class="w-2 h-2 rounded-full {{ $device['status'] === 'online' ? 'bg-green-500 animate-pulse' : ($device['status'] === 'offline' ? 'bg-gray-400' : ($device['status'] === 'error' ? 'bg-red-500' : 'bg-yellow-500')) }}"></span>
            {{ ucfirst($device['status'] ?? 'unknown') }}
        </span>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('lab-devices.edit', $device['id']) }}" 
               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-yellow-500 hover:bg-yellow-600 rounded-lg text-white text-xs font-medium transition-all"
               title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            <button onclick="testConnection({{ $device['id'] }}, '{{ addslashes($device['name']) }}')" 
                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-white text-xs font-medium transition-all"
                title="Test">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </button>
            <form action="{{ route('lab-devices.destroy', $device['id']) }}" method="POST" 
                  class="inline-block" onsubmit="return confirm('Delete {{ addslashes($device['name']) }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500 hover:bg-red-600 rounded-lg text-white text-xs font-medium transition-all"
                        title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@endforeach
