  @forelse($logs as $log)
                    <tr class="hover:bg-indigo-50/40 transition-all">

                        {{-- 👤 USER --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $userName = $log['user_name']
                                    ?? $log['user']['full_name']
                                    ?? $log['user']['name']
                                    ?? 'System';
                            @endphp

                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm shadow-sm">
                                    {{ strtoupper(substr($userName, 0, 1)) }}
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $userName }}
                                </div>
                            </div>
                        </td>

                        {{-- ⚡ ACTION --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $actionType = $log['action_type'] ?? 'unknown';

                                $colors = [
                                    'create_patient' => 'bg-green-100 text-green-800',
                                    'create_quotation' => 'bg-blue-100 text-blue-800',
                                    'create_analysis' => 'bg-purple-100 text-purple-800',
                                    'read_quotation' => 'bg-indigo-100 text-indigo-800',
                                    'login' => 'bg-emerald-100 text-emerald-800',
                                    'logout' => 'bg-gray-100 text-gray-700',
                                    'failed' => 'bg-red-100 text-red-800',
                                ];

                                $icons = [
                                    'create_patient' => '👤',
                                    'create_quotation' => '📄',
                                    'create_analysis' => '🔬',
                                    'read_quotation' => '📖',
                                    'login' => '🔑',
                                    'logout' => '🚪',
                                    'failed' => '❌',
                                ];

                                $isFailed = str_contains($actionType, 'failed');
                                $badgeColor = $isFailed ? $colors['failed'] : ($colors[$actionType] ?? 'bg-gray-100 text-gray-700');
                                $icon = $isFailed ? $icons['failed'] : ($icons[$actionType] ?? '📝');
                            @endphp

                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                {{ $icon }} {{ ucwords(str_replace('_', ' ', $actionType)) }}
                            </span>
                        </td>

                        {{-- 📝 DESCRIPTION --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-800 max-w-xs truncate" title="{{ $log['description'] ?? '' }}">
                                {{ $log['description'] ?? '—' }}
                            </div>
                        </td>

                        {{-- 🌍 IP --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 border border-slate-200 rounded-lg font-mono text-xs text-slate-700 shadow-sm">
                                🌍 {{ $log['ip_address'] ?? 'N/A' }}
                            </span>
                        </td>

                        {{-- 💻 DEVICE INFO - FIXED FOR BACKEND FORMAT --}}
                       {{-- 💻 DEVICE INFO - PRECISE & CLEAN --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $ua = $log['user_agent'] ?? 'NOT_SET';
                                    
                                    // DEBUG: Check if it contains the separator
                                    if (!str_contains($ua, '•')) {
                                        // Old format or missing - show raw
                                        $displayText = $ua;
                                        $icon = '❓';
                                        $badge = 'bg-red-100 text-red-700';
                                    } else {
                                        $parts = explode(' • ', $ua);
                                        $os = $parts[0] ?? 'Unknown';
                                        $browser = $parts[1] ?? 'Unknown';
                                        $device = $parts[2] ?? 'Desktop';
                                        
                                        $icon = '💻';
                                        $badge = 'bg-gray-100 text-gray-700';
                                        
                                        // API Tools
                                        if (str_contains($browser, 'API') || str_contains($browser, 'Guzzle')) {
                                            $icon = '🔧'; $badge = 'bg-yellow-100 text-yellow-800';
                                            $displayText = 'API Client';
                                        } elseif (str_contains($browser, 'Postman')) {
                                            $icon = '📡'; $badge = 'bg-purple-100 text-purple-700';
                                            $displayText = 'Postman';
                                        } elseif (str_contains($browser, 'cURL')) {
                                            $icon = '🔗'; $badge = 'bg-indigo-100 text-indigo-700';
                                            $displayText = 'cURL';
                                        } 
                                        // Mobile/Tablet
                                        elseif ($device === 'Mobile') {
                                            $icon = '📱'; $badge = 'bg-pink-100 text-pink-700';
                                            $displayText = "$browser Mobile";
                                        } elseif ($device === 'Tablet') {
                                            $icon = '📱'; $badge = 'bg-pink-100 text-pink-700';
                                            $displayText = "$browser Tablet";
                                        } 
                                        // Desktop
                                        else {
                                            if ($os !== 'Unknown') {
                                                $displayText = "$browser on $os";
                                            } else {
                                                $displayText = $browser;
                                            }
                                            
                                            if (str_contains($browser, 'Chrome')) {
                                                $icon = '🌐'; $badge = 'bg-green-100 text-green-700';
                                            } elseif (str_contains($browser, 'Edge')) {
                                                $icon = '🟦'; $badge = 'bg-blue-100 text-blue-800';
                                            } elseif (str_contains($browser, 'Firefox')) {
                                                $icon = '🦊'; $badge = 'bg-orange-100 text-orange-700';
                                            } elseif (str_contains($browser, 'Safari')) {
                                                $icon = '🧭'; $badge = 'bg-gray-200 text-gray-700';
                                            }
                                        }
                                    }
                                @endphp

                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium {{ $badge }}" 
                                    title="Raw: {{ $ua }}">
                                    {{ $icon }} {{ $displayText }}
                                </span>
                            </td>


                        {{-- 🕒 TIME --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($log['created_at'])->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($log['created_at'])->format('H:i:s') }}</span>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No activity logs found.
                        </td>
                    </tr>
                    @endforelse