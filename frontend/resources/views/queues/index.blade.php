@extends('layouts.app')

@section('title', 'Queue Management')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-red-50/30 to-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#ff5252] to-[#ff6b6b] rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users-line text-white text-xl"></i>
                    </div>
                    Queue Management
                </h1>
                <p class="mt-2 text-gray-600">Real-time patient flow control</p>
            </div>

            {{-- Stats Cards --}}
            <div class="flex gap-4">
                <div class="bg-white rounded-2xl shadow-xl px-6 py-4 border-l-4 border-[#ff5252] hover:shadow-2xl transition-all hover:-translate-y-1">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-[#ff5252]/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-door-open text-[#ff5252] text-xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900" id="reception-count">
                                {{ count($receptionQueue) }}
                            </div>
                            <div class="text-xs text-gray-600 font-medium">Reception</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl px-6 py-4 border-l-4 border-[#ff6b6b] hover:shadow-2xl transition-all hover:-translate-y-1">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-[#ff6b6b]/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-syringe text-[#ff6b6b] text-xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900" id="blood-draw-count">
                                {{ count($bloodDrawQueue) }}
                            </div>
                            <div class="text-xs text-gray-600 font-medium">Blood Draw</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto mb-6">
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-lg animate-fade-in relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 to-transparent"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="text-green-700 hover:text-green-900 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto mb-6">
            <div class="bg-red-50 border-l-4 border-[#ff5252] p-4 rounded-xl shadow-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-[#ff5252]/5 to-transparent"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-[#ff5252] text-xl mr-3"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-red-800 font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="text-red-700 hover:text-red-900 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Main Queue Area (3/4) --}}
        <div class="lg:col-span-3 space-y-6">
            
            {{-- Reception Queue --}}
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100" data-queue="reception">
                <div class="bg-gradient-to-r from-[#ff5252] via-[#ff5252]/95 to-[#ff6b6b] px-6 py-5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div class="text-white">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <i class="fas fa-door-open text-xl"></i>
                                </div>
                                <h2 class="text-2xl font-bold">Reception Queue</h2>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-white/90">
                                <span class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm">
                                    <i class="fas fa-users"></i>
                                    <span id="reception-waiting">{{ count($receptionQueue) }}</span> waiting
                                </span>
                                @if(count($receptionQueue) > 0)
                                    <span class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm">
                                        <i class="fas fa-clock"></i>
                                        ~<span id="reception-wait">{{ count($receptionQueue) * 7 }}</span> min
                                    </span>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('queues.moveNext') }}" id="moveNextForm">
                            @csrf
                            <button type="submit" 
                                    id="moveNextBtn"
                                    class="px-6 py-3 bg-white text-[#ff5252] font-bold rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center gap-2 group"
                                    {{ count($receptionQueue) === 0 ? 'disabled' : '' }}>
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                <span>Call Next</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-[#ff5252]/20 scrollbar-track-gray-100">
                    @forelse($receptionQueue as $item)
                        <div class="p-5 hover:bg-[#f1f3f5] transition-all group" data-queue-id="{{ $item['id'] }}">
                            <div class="flex items-center justify-between">
                                
                                {{-- Left: Position & Info --}}
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="relative">
                                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg transition-transform group-hover:scale-105
                                            {{ $item['priority'] == 2 ? 'bg-gradient-to-br from-red-600 to-red-700 animate-pulse shadow-red-500/50' : 
                                               ($item['priority'] == 1 ? 'bg-gradient-to-br from-orange-500 to-orange-600' : 
                                               'bg-gradient-to-br from-[#ff5252] to-[#ff6b6b]') }}">
                                            {{ $item['position'] }}
                                        </div>
                                        @if($item['priority'] > 0)
                                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                                                <i class="fas fa-exclamation text-red-700 text-xs"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-bold text-gray-900">
                                                {{ $item['patient_name'] ?? 'Patient #' . $item['patient_id'] }}
                                            </h3>
                                            @if($item['priority'] == 2)
                                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full flex items-center gap-1.5 animate-pulse">
                                                    <i class="fas fa-ambulance"></i> EMERGENCY
                                                </span>
                                            @elseif($item['priority'] == 1)
                                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                                                    <i class="fas fa-star"></i> URGENT
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            @if(isset($item['quotation_id']) && $item['quotation_id'])
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fas fa-file-invoice text-[#ff5252]"></i>
                                                    Quote #{{ $item['quotation_id'] }}
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-clock text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                            </span>
                                            @if(isset($item['estimated_wait_minutes']))
                                                <span class="flex items-center gap-1.5 text-[#ff6b6b] font-medium">
                                                    <i class="fas fa-hourglass-half"></i>
                                                    ~{{ $item['estimated_wait_minutes'] }} min
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Actions --}}
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <div class="flex gap-1 bg-[#f1f3f5] rounded-xl p-1">
                                        <button onclick="updatePriority({{ $item['id'] }}, 2)" 
                                                class="w-10 h-10 rounded-lg hover:bg-red-100 text-red-600 transition-all hover:scale-110 flex items-center justify-center"
                                                title="Emergency">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                        <button onclick="updatePriority({{ $item['id'] }}, 1)" 
                                                class="w-10 h-10 rounded-lg hover:bg-orange-100 text-orange-600 transition-all hover:scale-110 flex items-center justify-center"
                                                title="Urgent">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        <button onclick="updatePriority({{ $item['id'] }}, 0)" 
                                                class="w-10 h-10 rounded-lg hover:bg-gray-200 text-gray-600 transition-all hover:scale-110 flex items-center justify-center"
                                                title="Normal">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Remove this patient from queue?')"
                                                class="w-10 h-10 rounded-lg hover:bg-red-100 text-red-600 transition-all hover:scale-110 flex items-center justify-center"
                                                title="Remove">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="w-24 h-24 bg-[#f1f3f5] rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                                <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                            </div>
                            <p class="text-xl font-semibold text-gray-400">No patients waiting</p>
                            <p class="text-sm text-gray-400 mt-1">Reception queue is empty</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Blood Draw Queue --}}
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100" data-queue="blood-draw">
                <div class="bg-gradient-to-r from-[#ff6b6b] via-[#ff6b6b]/95 to-[#ff5252] px-6 py-5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                    <div class="text-white relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-syringe text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold">Blood Draw Room</h2>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-white/90">
                            <span class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm">
                                <i class="fas fa-users"></i>
                                <span id="blood-draw-waiting">{{ count($bloodDrawQueue) }}</span> waiting
                            </span>
                            @if(count($bloodDrawQueue) > 0)
                                <span class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm">
                                    <i class="fas fa-clock"></i>
                                    ~<span id="blood-draw-wait">{{ count($bloodDrawQueue) * 5 }}</span> min
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-[#ff6b6b]/20 scrollbar-track-gray-100">
                    @forelse($bloodDrawQueue as $item)
                        <div class="p-5 hover:bg-[#f1f3f5] transition-all group
                                    {{ $item['status'] == 'called' ? 'bg-yellow-50/50 border-l-4 border-yellow-500' : '' }}
                                    {{ $item['status'] == 'in_progress' ? 'bg-green-50/50 border-l-4 border-green-500' : '' }}"
                             data-queue-id="{{ $item['id'] }}">
                            <div class="flex items-center justify-between">
                                
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg transition-transform group-hover:scale-105
                                        {{ $item['priority'] == 2 ? 'bg-gradient-to-br from-red-600 to-red-700 animate-pulse shadow-red-500/50' : 
                                           ($item['priority'] == 1 ? 'bg-gradient-to-br from-orange-500 to-orange-600' : 
                                           'bg-gradient-to-br from-[#ff6b6b] to-[#ff5252]') }}">
                                        {{ $item['position'] }}
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-bold text-gray-900">
                                                {{ $item['patient_name'] ?? 'Patient #' . $item['patient_id'] }}
                                            </h3>
                                            @if($item['status'] == 'called')
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full animate-pulse flex items-center gap-1.5">
                                                    <i class="fas fa-bell"></i> CALLED
                                                </span>
                                            @elseif($item['status'] == 'in_progress')
                                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                                                    <i class="fas fa-spinner fa-spin"></i> IN PROGRESS
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            @if(isset($item['quotation_id']) && $item['quotation_id'])
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fas fa-file-invoice text-[#ff6b6b]"></i>
                                                    Quote #{{ $item['quotation_id'] }}
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-clock text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <button onclick="markComplete({{ $item['id'] }})" 
                                            class="w-10 h-10 rounded-lg hover:bg-green-100 text-green-600 transition-all hover:scale-110 flex items-center justify-center"
                                            title="Mark Complete">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </button>

                                    <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Remove this patient from queue?')"
                                                class="w-10 h-10 rounded-lg hover:bg-red-100 text-red-600 transition-all hover:scale-110 flex items-center justify-center"
                                                title="Remove">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="w-24 h-24 bg-[#f1f3f5] rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                                <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                            </div>
                            <p class="text-xl font-semibold text-gray-400">No patients waiting</p>
                            <p class="text-sm text-gray-400 mt-1">Blood draw room is free</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Add Patient Form (1/4) --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-2xl p-6 sticky top-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#ff5252] to-[#ff6b6b] rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Add Patient</h2>
                </div>

                <form method="POST" action="{{ route('queues.store') }}" class="space-y-5">
                    @csrf

                    {{-- Patient Select --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-[#ff5252] mr-1"></i> Patient
                        </label>
                        <select name="patient_id" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff5252] focus:border-[#ff5252] transition">
                            <option value="">Select patient...</option>
                            @foreach($patients as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Queue Type --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-list text-[#ff5252] mr-1"></i> Queue Type
                        </label>
                        <select name="queue_type" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff5252] focus:border-[#ff5252] transition">
                            <option value="">Choose queue...</option>
                            <option value="reception">Reception</option>
                            <option value="blood_draw">Blood Draw</option>
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-exclamation-circle text-[#ff5252] mr-1"></i> Priority
                        </label>
                        <select name="priority"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff5252] focus:border-[#ff5252] transition">
                            <option value="0">Normal</option>
                            <option value="1">Urgent</option>
                            <option value="2">Emergency</option>
                        </select>
                    </div>

                    {{-- Quotation ID (Optional) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-file-invoice text-[#ff5252] mr-1"></i> Quotation ID (Optional)
                        </label>
                        <input type="number" name="quotation_id" placeholder="Enter quotation ID"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff5252] focus:border-[#ff5252] transition">
                    </div>

                    {{-- Notes (Optional) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sticky-note text-[#ff5252] mr-1"></i> Notes (Optional)
                        </label>
                        <textarea name="notes" rows="3" placeholder="Add any notes..."
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff5252] focus:border-[#ff5252] transition resize-none"></textarea>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-[#ff5252] to-[#ff6b6b] hover:from-[#ff5252]/90 hover:to-[#ff6b6b]/90 text-white font-bold py-4 rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add to Queue</span>
                    </button>
                </form>

                {{-- Quick Links --}}
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <a href="{{ route('queues.show') }}" target="_blank"
                       class="block text-center bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        <i class="fas fa-tv mr-2"></i> Waiting Room Display
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="block text-center bg-[#f1f3f5] hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition-all hover:-translate-y-0.5">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </div>
            </div>
        </aside>
    </div>

</div>

{{-- Audio for notifications --}}
<audio id="notification-sound" preload="auto">
    <source src="/audio/notify.mp3" type="audio/mpeg">
</audio>

<script>
    window.routes = {
        moveNext: @json(route('queues.moveNext')),
        queuesIndex: @json(route('queues.index'))
    };
</script>

<script>
const audio = document.getElementById('notification-sound');

// Override pause() to log who's calling it
const originalPause = audio.pause.bind(audio);
audio.pause = function() {
    console.error('🚨 PAUSE CALLED! Stack trace:');
    console.trace();
    return originalPause();
};

// Override load() too (it also stops playback)
const originalLoad = audio.load.bind(audio);
audio.load = function() {
    console.error('🚨 LOAD CALLED! Stack trace:');
    console.trace();
    return originalLoad();
};
</script>
@endsection
@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// Move Next - AJAX version (no page reload)
let isPlayingNotification = false;
document.getElementById('moveNextForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('moveNextBtn');
    const audio = document.getElementById('notification-sound');

    if (btn.disabled || isPlayingNotification) return;
    
    console.log('🔄 Appel du patient suivant...');
    
    // Disable button
    btn.disabled = true;
    isPlayingNotification = true; // Set flag
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> [translate:En cours...]';
    
    let audioEnded = false;
    
    if (audio) {
        audio.addEventListener('ended', function() {
            console.log('🎵 Audio ended event fired');
            audioEnded = true;
            isPlayingNotification = false; // Release flag
        }, { once: true });
        
        audio.addEventListener('error', function(e) {
            console.error('❌ Audio error:', e);
            audioEnded = true;
            isPlayingNotification = false; // Release flag
        }, { once: true });
        
        audio.currentTime = 0;
        
        const playPromise = audio.play();
        
        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    console.log('✅ Audio started - Duration:', audio.duration);
                    // CRITICAL: Check if something paused it immediately
                    setTimeout(() => {
                        if (audio.paused && !audioEnded) {
                            console.error('⚠️ Audio was paused externally! Resuming...');
                            audio.play();
                        }
                    }, 50);
                })
                .catch(err => {
                    console.error('❌ Audio blocked:', err.message);
                    audioEnded = true;
                    isPlayingNotification = false;
                });
        }
    }
    
    // Call API
    fetch(window.routes.moveNext, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Réponse reçue:', data);
        
        if (data.success || data.patient_name) {
            const patient = {
                id: data.id || data.queue_id,
                patient_id: data.patient_id,
                patient_name: data.patient_name || 'Patient',
                position: data.position || 1
            };
            
            let ttsLaunched = false;
            let waitCount = 0;
            
            const launchTTS = () => {
                if (!ttsLaunched) {
                    ttsLaunched = true;
                    console.log('🗣️ Launching TTS for:', patient.patient_name);
                    speakFrenchAnnouncement(patient);
                    isPlayingNotification = false; // Release when done
                }
            };
            
            const waitForAudio = () => {
                waitCount++;
                
                if (audioEnded || !audio) {
                    console.log('⏭️ Audio done, refreshing queues');
                    refreshQueues();
                    launchTTS();
                } else if (waitCount > 20) { // Reduce to 2 seconds since audio is 1.7s
                    console.warn('⏱️ Timeout - forcing TTS');
                    audioEnded = true;
                    refreshQueues();
                    launchTTS();
                } else {
                    setTimeout(waitForAudio, 100);
                }
            };
            
            waitForAudio();
            
            showToast(`${patient.patient_name} [translate:appelé(e) à la salle de prélèvement]`, 'success');
            
        } else {
            showToast(data.message || '[translate:Échec du déplacement]', 'error');
            btn.disabled = false;
            isPlayingNotification = false;
            btn.innerHTML = '<i class="fas fa-arrow-right mr-2></i> [translate:Appeler suivant]';
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        showToast('[translate:Erreur réseau. Veuillez réessayer.]', 'error');
        btn.disabled = false;
        isPlayingNotification = false;
        btn.innerHTML = '<i class="fas fa-arrow-right mr-2></i> [translate:Appeler suivant]';
    });
});


// Refresh queues display
function refreshQueues() {
    console.log('🔄 Actualisation des files...');
    
    fetch(window.routes.queuesIndex, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Update reception queue
        const newReceptionQueue = doc.querySelector('[data-queue="reception"]');
        const currentReceptionQueue = document.querySelector('[data-queue="reception"]');
        if (newReceptionQueue && currentReceptionQueue) {
            currentReceptionQueue.innerHTML = newReceptionQueue.innerHTML;
        }
        
        // Update blood draw queue
        const newBloodDrawQueue = doc.querySelector('[data-queue="blood-draw"]');
        const currentBloodDrawQueue = document.querySelector('[data-queue="blood-draw"]');
        if (newBloodDrawQueue && currentBloodDrawQueue) {
            currentBloodDrawQueue.innerHTML = newBloodDrawQueue.innerHTML;
        }
        
        // Update counts
        updateQueueCounts();
        
        console.log('✅ Files actualisées');
        
        // Re-enable button
        const btn = document.getElementById('moveNextBtn');
        if (btn) {
            const receptionCount = parseInt(document.getElementById('reception-count')?.textContent || 0);
            btn.disabled = receptionCount === 0;
            btn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i> [translate:Appeler suivant]';
        }
    })
    .catch(err => {
        console.error('❌ Erreur actualisation:', err);
        // Fallback: reload page
        location.reload();
    });
}

// Update queue counts
function updateQueueCounts() {
    fetch('/api/queues/status', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.reception && data.blood_draw) {
            const receptionTotal = (data.reception.total_waiting || 0) + (data.reception.total_called || 0);
            const bloodDrawTotal = (data.blood_draw.total_waiting || 0) + (data.blood_draw.total_called || 0);
            
            document.getElementById('reception-count').textContent = receptionTotal;
            document.getElementById('blood-draw-count').textContent = bloodDrawTotal;
            document.getElementById('reception-waiting').textContent = receptionTotal;
            document.getElementById('blood-draw-waiting').textContent = bloodDrawTotal;
            
            if (data.reception.estimated_total_wait) {
                document.getElementById('reception-wait').textContent = data.reception.estimated_total_wait;
            }
            if (data.blood_draw.estimated_total_wait) {
                document.getElementById('blood-draw-wait').textContent = data.blood_draw.estimated_total_wait;
            }
        }
    })
    .catch(err => console.warn('Count update failed:', err));
}

// Speak French announcement
function speakFrenchAnnouncement(patient) {
    const message = `Numéro ${patient.position}. ${patient.patient_name}, veuillez vous présenter à la salle de prélèvement sanguin. Merci.`;
    
    const utterance = new SpeechSynthesisUtterance(message);
    utterance.lang = 'fr-FR';
    utterance.rate = 0.85;
    utterance.pitch = 1.0;
    utterance.volume = 1.0;
    
    function getBestFrenchVoice() {
        const voices = window.speechSynthesis.getVoices();
        
        const preferredNames = [
            'Google français',
            'Microsoft Hortense',
            'Microsoft Julie',
            'Google French',
            'Amélie',
            'Thomas'
        ];
        
        for (let name of preferredNames) {
            const voice = voices.find(v => v.name.includes(name));
            if (voice) {
                console.log('🎙️ Voix:', voice.name);
                return voice;
            }
        }
        
        const anyFrench = voices.find(v => v.lang.startsWith('fr'));
        if (anyFrench) {
            console.log('🎙️ Voix française:', anyFrench.name);
            return anyFrench;
        }
        
        console.warn('⚠️ Aucune voix française');
        return null;
    }
    
    utterance.onstart = () => console.log('🗣️ Annonce vocale démarrée');
    utterance.onend = () => console.log('✅ Annonce vocale terminée');
    utterance.onerror = (e) => console.error('❌ Erreur vocale:', e.error);
    
    const voices = window.speechSynthesis.getVoices();
    
    if (voices.length === 0) {
        window.speechSynthesis.addEventListener('voiceschanged', function handler() {
            utterance.voice = getBestFrenchVoice();
            window.speechSynthesis.speak(utterance);
            window.speechSynthesis.removeEventListener('voiceschanged', handler);
        });
    } else {
        utterance.voice = getBestFrenchVoice();
        window.speechSynthesis.speak(utterance);
    }
}

// Update Priority - AJAX
function updatePriority(queueId, priority) {
    if (!confirm('[translate:Changer la priorité de ce patient ?]')) return;
    
    fetch(`/queues/${queueId}/priority`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ priority: priority })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('[translate:Priorité mise à jour]', 'success');
            refreshQueues();
        } else {
            showToast(data.message || '[translate:Échec]', 'error');
        }
    })
    .catch(error => {
        showToast('[translate:Erreur réseau]', 'error');
        console.error('Error:', error);
    });
}

// Mark Complete - AJAX
function markComplete(queueId) {
    if (!confirm('[translate:Marquer ce patient comme terminé ?]')) return;
    
    fetch(`/queues/${queueId}?reason=completed`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            showToast('[translate:Patient retiré de la file]', 'success');
            refreshQueues();
        } else {
            showToast('[translate:Échec de la suppression]', 'error');
        }
    })
    .catch(error => {
        showToast('[translate:Erreur réseau]', 'error');
        console.error('Error:', error);
    });
}

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-xl shadow-2xl text-white font-semibold z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.opacity = '0', 3000);
    setTimeout(() => toast.remove(), 3500);
}

// Auto-refresh every 30 seconds
setInterval(() => {
    updateQueueCounts();
}, 30000);
</script>
@endpush

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #ff5252;
    border-radius: 3px;
    opacity: 0.2;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #ff6b6b;
}

/* Smooth hover transitions */
@media (prefers-reduced-motion: no-preference) {
    * {
        scroll-behavior: smooth;
    }
}
</style>
