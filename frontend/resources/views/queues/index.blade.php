@extends('layouts.app')

@section('title', 'Queue Management')

@section('content')
<div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- TOP BAR / HERO --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Live queue
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users-line text-white text-lg"></i>
                    </span>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                            Queue Management
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">
                            Real-time control of reception and blood draw flow.
                        </p>
                    </div>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full lg:w-auto">
                {{-- Reception --}}
                <div class="relative overflow-hidden rounded-2xl bg-white shadow-md border border-slate-200/70 hover:shadow-lg transition-shadow">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-500/10 rounded-full blur-xl"></div>
                    <div class="p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <i class="fas fa-door-open text-[#ff5252] text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Reception</p>
                            <p class="flex items-baseline gap-1">
                                <span class="text-2xl font-extrabold text-slate-900" id="reception-count">
                                    {{ count($receptionQueue) }}
                                </span>
                                <span class="text-xs text-slate-500">in queue</span>
                            </p>
                            @php
                                $nextRecTicket = !empty($receptionQueue)
                                    ? ($receptionQueue[0]['ticket_number'] ?? 1)
                                    : ($counters['reception_next'] ?? 1);
                            @endphp
                            <p class="text-xs text-slate-500 mt-1">
                                Next ticket:
                                <span class="font-bold text-[#ff5252]">
                                    N°{{ sprintf('%02d', $nextRecTicket) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Blood Draw --}}
                <div class="relative overflow-hidden rounded-2xl bg-white shadow-md border border-slate-200/70 hover:shadow-lg transition-shadow">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-orange-500/10 rounded-full blur-xl"></div>
                    <div class="p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <i class="fas fa-syringe text-[#ff6b6b] text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Blood draw</p>
                            <p class="flex items-baseline gap-1">
                                <span class="text-2xl font-extrabold text-slate-900" id="blood-draw-count">
                                    {{ count($bloodDrawQueue) }}
                                </span>
                                <span class="text-xs text-slate-500">in queue</span>
                            </p>
                            @php
                                $nextBdTicket = !empty($bloodDrawQueue)
                                    ? ($bloodDrawQueue[0]['ticket_number'] ?? 1)
                                    : 1;
                            @endphp
                            <p class="text-xs text-slate-500 mt-1">
                                Next ticket:
                                <span class="font-bold text-[#ff6b6b]">
                                    N°{{ sprintf('%02d', $nextBdTicket) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="flex items-center justify-between px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                <span class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    {{ session('success') }}
                </span>
                <button onclick="this.closest('div').remove()" class="text-emerald-400 hover:text-emerald-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="flex items-center justify-between px-4 py-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm">
                <span class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </span>
                <button onclick="this.closest('div').remove()" class="text-red-400 hover:text-red-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- QUEUES COLUMN --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- RECEPTION QUEUE --}}
                <section class="bg-white rounded-3xl border border-slate-200/70 shadow-md overflow-hidden" data-queue="reception">
                    <header class="px-5 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-[#ff5252]/10 flex items-center justify-center">
                                <i class="fas fa-door-open text-[#ff5252]"></i>
                            </span>
                            <div>
                                <h2 class="font-bold text-slate-900 text-sm md:text-base">Reception queue</h2>
                                <p class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span id="reception-waiting">{{ count($receptionQueue) }}</span> waiting
                                    @if(count($receptionQueue) > 0)
                                        <span class="text-slate-300">·</span>
                                        ~<span id="reception-wait">
                                            {{ $status['reception']['estimated_total_wait'] ?? count($receptionQueue) * 7 }}
                                        </span> min total
                                    @endif
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('queues.moveNext') }}" id="moveNextForm" class="flex-shrink-0">
                            @csrf
                            <button type="submit" id="moveNextBtn"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#ff5252] hover:bg-[#e04747] shadow-sm hover:shadow-md transition-all disabled:opacity-40 disabled:cursor-not-allowed group {{ count($receptionQueue) === 0 ? 'disabled' : '' }}">
                                <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                                <span>Call next</span>
                            </button>
                        </form>
                    </header>

                    <div class="max-h-[460px] overflow-y-auto scrollbar-thin divide-y divide-slate-100">
                        @forelse($receptionQueue as $item)
                            <article class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/80 transition-colors group" data-queue-id="{{ $item['id'] }}">
                                {{-- Ticket --}}
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-base shadow-sm
                                        {{ $item['priority'] == 2 ? 'bg-red-600 animate-pulse' : ($item['priority'] == 1 ? 'bg-orange-500' : 'bg-[#ff5252]') }}">
                                        N°{{ sprintf('%02d', $item['ticket_number'] ?? 0) }}
                                    </div>
                                    @if($item['priority'] > 0)
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center shadow">
                                            <i class="fas fa-exclamation text-red-700 text-[8px]"></i>
                                        </span>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-slate-900 text-sm truncate">
                                            {{ $item['patient_name'] ?? 'Patient #' . $item['patient_id'] }}
                                        </span>
                                        @if($item['priority'] == 2)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[11px] font-bold rounded-full animate-pulse">
                                                <i class="fas fa-ambulance text-[9px]"></i> EMERGENCY
                                            </span>
                                        @elseif($item['priority'] == 1)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-700 text-[11px] font-semibold rounded-full">
                                                <i class="fas fa-star text-[9px]"></i> URGENT
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-0.5 text-[11px] text-slate-400">
                                        @if(isset($item['quotation_id']) && $item['quotation_id'])
                                            <span><i class="fas fa-file-invoice mr-1 text-[#ff5252]/70"></i>Quote #{{ $item['quotation_id'] }}</span>
                                        @endif
                                        <span><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
                                        @if(isset($item['estimated_wait_minutes']))
                                            <span class="text-[#ff6b6b] font-medium">
                                                <i class="fas fa-hourglass-half mr-1"></i>~{{ $item['estimated_wait_minutes'] }} min
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150 shrink-0">
                                    <div class="flex items-center gap-0.5 bg-slate-100 rounded-lg p-1">
                                        <button onclick="updatePriority({{ $item['id'] }}, 2)" title="Emergency"
                                            class="w-7 h-7 rounded-md hover:bg-red-100 text-red-500 transition flex items-center justify-center text-[11px]">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                        <button onclick="updatePriority({{ $item['id'] }}, 1)" title="Urgent"
                                            class="w-7 h-7 rounded-md hover:bg-orange-100 text-orange-500 transition flex items-center justify-center text-[11px]">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        <button onclick="updatePriority({{ $item['id'] }}, 0)" title="Normal"
                                            class="w-7 h-7 rounded-md hover:bg-slate-200 text-slate-500 transition flex items-center justify-center text-[11px]">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Remove this patient from queue?')"
                                            class="w-7 h-7 rounded-md hover:bg-red-100 text-red-400 hover:text-red-600 transition flex items-center justify-center text-[11px]">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="py-14 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block opacity-30"></i>
                                <p class="font-medium text-sm">No patients waiting</p>
                                <p class="text-xs mt-1 opacity-70">Reception queue is empty</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- BLOOD DRAW QUEUE --}}
                <section class="bg-white rounded-3xl border border-slate-200/70 shadow-md overflow-hidden" data-queue="blood-draw">
                    <header class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-[#ff6b6b]/10 flex items-center justify-center">
                                <i class="fas fa-syringe text-[#ff6b6b]"></i>
                            </span>
                            <div>
                                <h2 class="font-bold text-slate-900 text-sm md:text-base">Blood draw room</h2>
                                <p class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span id="blood-draw-waiting">{{ count($bloodDrawQueue) }}</span> waiting
                                    @if(count($bloodDrawQueue) > 0)
                                        <span class="text-slate-300">·</span>
                                        ~<span id="blood-draw-wait">
                                            {{ $status['blood_draw']['estimated_total_wait'] ?? count($bloodDrawQueue) * 5 }}
                                        </span> min total
                                    @endif
                                </p>
                            </div>
                        </div>
                    </header>

                    <div class="max-h-[460px] overflow-y-auto scrollbar-thin divide-y divide-slate-100">
                        @forelse($bloodDrawQueue as $item)
                            <article class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/80 transition-colors group
                                {{ $item['status'] == 'called' ? 'border-l-2 border-yellow-400 bg-yellow-50/40' : '' }}
                                {{ $item['status'] == 'in_progress' ? 'border-l-2 border-emerald-400 bg-emerald-50/40' : '' }}"
                                data-queue-id="{{ $item['id'] }}">

                                {{-- Ticket --}}
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-base shadow-sm
                                        {{ $item['priority'] == 2 ? 'bg-red-600 animate-pulse' : ($item['priority'] == 1 ? 'bg-orange-500' : 'bg-[#ff6b6b]') }}">
                                        N°{{ sprintf('%02d', $item['ticket_number'] ?? 0) }}
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-slate-900 text-sm truncate">
                                            {{ $item['patient_name'] ?? 'Patient #' . $item['patient_id'] }}
                                        </span>
                                        @if($item['status'] == 'called')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-[11px] font-bold rounded-full animate-pulse">
                                                <i class="fas fa-bell text-[9px]"></i> CALLED
                                            </span>
                                        @elseif($item['status'] == 'in_progress')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-full">
                                                <i class="fas fa-spinner fa-spin text-[9px]"></i> IN PROGRESS
                                            </span>
                                        @endif
                                        @if($item['priority'] == 2)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[11px] font-bold rounded-full animate-pulse">
                                                <i class="fas fa-ambulance text-[9px]"></i> EMERGENCY
                                            </span>
                                        @elseif($item['priority'] == 1)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-700 text-[11px] font-semibold rounded-full">
                                                <i class="fas fa-star text-[9px]"></i> URGENT
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-0.5 text-[11px] text-slate-400">
                                        @if(isset($item['quotation_id']) && $item['quotation_id'])
                                            <span><i class="fas fa-file-invoice mr-1 text-[#ff6b6b]/70"></i>Quote #{{ $item['quotation_id'] }}</span>
                                        @endif
                                        <span><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150 shrink-0">
                                    <button onclick="markComplete({{ $item['id'] }})" title="Mark complete"
                                        class="w-7 h-7 rounded-md hover:bg-emerald-100 text-emerald-500 transition flex items-center justify-center text-sm">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Remove this patient?')"
                                            class="w-7 h-7 rounded-md hover:bg-red-100 text-red-400 hover:text-red-600 transition flex items-center justify-center text-[11px]">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="py-14 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block opacity-30"></i>
                                <p class="font-medium text-sm">No patients waiting</p>
                                <p class="text-xs mt-1 opacity-70">Blood draw room is free</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- SIDEBAR --}}
            <aside class="xl:col-span-1">
                <div class="bg-white rounded-3xl border border-slate-200/70 shadow-md p-5 space-y-5 sticky top-6">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 bg-[#ff5252]/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-plus text-[#ff5252] text-sm"></i>
                        </span>
                        <div>
                            <h2 class="font-bold text-slate-900 text-sm">Add patient to queue</h2>
                            <p class="text-xs text-slate-500">Quick add with priority and notes.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('queues.store') }}" class="space-y-3">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                <i class="fas fa-user text-slate-400 mr-1"></i> Patient
                            </label>
                            <select name="patient_id" required
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5252]/20 focus:border-[#ff5252] transition bg-slate-50/50">
                                <option value="">Select patient...</option>
                                @foreach($patients as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    <i class="fas fa-list text-slate-400 mr-1"></i> Queue
                                </label>
                                <select name="queue_type" required
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5252]/20 focus:border-[#ff5252] transition bg-slate-50/50">
                                    <option value="">Choose...</option>
                                    <option value="reception">Reception</option>
                                    <option value="blood_draw">Blood Draw</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    <i class="fas fa-exclamation-circle text-slate-400 mr-1"></i> Priority
                                </label>
                                <select name="priority"
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5252]/20 focus:border-[#ff5252] transition bg-slate-50/50">
                                    <option value="0">Normal</option>
                                    <option value="1">Urgent</option>
                                    <option value="2">Emergency</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                <i class="fas fa-file-invoice text-slate-400 mr-1"></i> Quotation ID
                                <span class="font-normal text-slate-400">(optional)</span>
                            </label>
                            <input type="number" name="quotation_id" placeholder="e.g. 42"
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5252]/20 focus:border-[#ff5252] transition bg-slate-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                <i class="fas fa-sticky-note text-slate-400 mr-1"></i> Notes
                                <span class="font-normal text-slate-400">(optional)</span>
                            </label>
                            <textarea name="notes" rows="2" placeholder="Add any notes..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#ff5252]/20 focus:border-[#ff5252] transition bg-slate-50/50 resize-none"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-[#ff5252] hover:bg-[#e04747] text-white text-sm font-bold py-3 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Add to queue</span>
                        </button>
                    </form>

                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        <a href="{{ route('queues.show') }}" target="_blank"
                            class="flex items-center justify-center gap-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-950 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fas fa-tv text-xs"></i> Waiting room display
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center justify-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 py-2.5 rounded-xl transition">
                            <i class="fas fa-home text-xs"></i> Back to dashboard
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>


{{-- Audio --}}
<audio id="notification-sound" preload="auto">
    <source src="{{ asset('audio/notify.mp3') }}" type="audio/mpeg">
</audio>
<script src="https://code.responsivevoice.org/responsivevoice.js?key=trVmOMt9"></script>
<script>
window.routes = {
    moveNext: @json(route('queues.moveNext')),
    queuesIndex: @json(route('queues.index'))
};
window.apiBase = @json(env('FASTAPI_URL', 'http://localhost:8000'));
</script>

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let isPlayingNotification = false;

// ─── Move Next ──────────────────────────────────────────────────────────────
document.getElementById('moveNextForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('moveNextBtn');
    const audio = document.getElementById('notification-sound');

    if (btn.disabled || isPlayingNotification) return;

    btn.disabled = true;
    isPlayingNotification = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-xs"></i> En cours...';

    let audioEnded = false;

    if (audio) {
        audio.addEventListener('ended', () => { audioEnded = true; isPlayingNotification = false; }, { once: true });
        audio.addEventListener('error', () => { audioEnded = true; isPlayingNotification = false; }, { once: true });
        audio.currentTime = 0;
        audio.play().catch(err => { console.warn('Audio blocked:', err.message); audioEnded = true; isPlayingNotification = false; });
    }

    fetch('/queues/move-next', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success || data.patient_name) {
            const patient = {
                patient_name: data.patient_name || 'Patient',
                ticket_number: data.ticket_number || data.position || 1
            };

            showToast(`${patient.patient_name} appelé(e) — N°${String(patient.ticket_number).padStart(2,'0')}`, 'success');

            let waited = 0;
            const waitForAudio = () => {
                waited++;
                if (audioEnded || !audio || waited > 25) {
                    refreshQueues();
                    speakFrenchAnnouncement(patient);
                } else {
                    setTimeout(waitForAudio, 100);
                }
            };
            waitForAudio();
        } else {
            showToast(data.message || 'Déplacement échoué', 'error');
            resetBtn(btn);
        }
    })
    .catch(err => {
        console.error('Move next error:', err);
        showToast('Erreur réseau. Réessayez.', 'error');
        resetBtn(btn);
    });
});

function resetBtn(btn) {
    btn.disabled = false;
    isPlayingNotification = false;
    btn.innerHTML = '<i class="fas fa-arrow-right mr-1 text-xs"></i> Call Next';
}

// ─── Refresh Queues ─────────────────────────────────────────────────────────
function refreshQueues() {
    fetch(window.routes.queuesIndex, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
    })
    .then(r => r.text())
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');

        // ✅ Replace both queue panels
        ['reception', 'blood-draw'].forEach(key => {
            const fresh = doc.querySelector(`[data-queue="${key}"]`);
            const current = document.querySelector(`[data-queue="${key}"]`);
            if (fresh && current) current.innerHTML = fresh.innerHTML;
        });

        updateQueueCounts();

        const btn = document.getElementById('moveNextBtn');
        if (btn) {
            const count = parseInt(document.getElementById('reception-count')?.textContent || 0);
            btn.disabled = count === 0;
            btn.innerHTML = count === 0
                ? '<i class="fas fa-inbox mr-1 text-xs"></i> No patients'
                : '<i class="fas fa-arrow-right mr-1 text-xs"></i> Call Next';
        }
        isPlayingNotification = false;
    })
    .catch(() => location.reload());
}

// ─── Update Counts ───────────────────────────────────────────────────────────
function updateQueueCounts() {
    fetch(`${window.apiBase}/queues/status`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.reception || !data.blood_draw) return;

        const rec = (data.reception.total_waiting || 0) + (data.reception.total_called || 0);
        const bd  = (data.blood_draw.total_waiting || 0) + (data.blood_draw.total_called || 0);

        document.getElementById('reception-count').textContent  = rec;
        document.getElementById('blood-draw-count').textContent = bd;
        document.getElementById('reception-waiting').textContent  = rec;
        document.getElementById('blood-draw-waiting').textContent = bd;

        const recWait = data.reception.estimated_total_wait;
        const bdWait  = data.blood_draw.estimated_total_wait;
        if (recWait) document.getElementById('reception-wait').textContent  = recWait;
        if (bdWait)  document.getElementById('blood-draw-wait').textContent = bdWait;
    })
    .catch(err => console.warn('Count fetch failed:', err));
}

// ─── Update Priority ─────────────────────────────────────────────────────────
function updatePriority(queueId, priority) {
    if (!confirm('Changer la priorité de ce patient ?')) return;

    fetch(`/queues/${queueId}/priority?priority=${priority}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast('Priorité mise à jour', 'success'); refreshQueues(); }
        else showToast(data.message || 'Échec', 'error');
    })
    .catch(() => showToast('Erreur réseau', 'error'));
}

// ─── Mark Complete ───────────────────────────────────────────────────────────
function markComplete(queueId) {
    if (!confirm('Marquer ce patient comme terminé ?')) return;

    fetch(`/queues/${queueId}?reason=completed`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(r => {
        if (r.ok) { showToast('Patient retiré de la file', 'success'); refreshQueues(); }
        else showToast('Échec de la suppression', 'error');
    })
    .catch(() => showToast('Erreur réseau', 'error'));
}

// ─── TTS Announcement ────────────────────────────────────────────────────────
function speakFrenchAnnouncement(patient) {
    // ✅ VALIDATE ticket_number FIRST
    const num = Number(patient.ticket_number);
    if (isNaN(num) || num <= 0) {
        console.log('⏭️ Invalid ticket:', patient.ticket_number);
        return;
    }
    
    const numStr = num.toString().padStart(2, '0');
    const msg = `Numéro ${numStr}. ${patient.patient_name || 'Patient'}, veuillez vous présenter à la salle de prélèvement sanguin. Merci.`;

    console.log('🔊 Announcing:', { num: numStr, msg });

    if (typeof responsiveVoice !== 'undefined') {
        responsiveVoice.speak(msg, 'French Female', { 
            rate: 0.9, 
            pitch: 0.9,
            onstart: () => console.log('✅ RV Speaking:', msg),
            onend: () => console.log('✅ Announcement done')
        });
    } else {
        // Native fallback
        const utterance = new SpeechSynthesisUtterance(msg);
        utterance.lang = 'fr-FR';
        utterance.rate = 0.9;
        speechSynthesis.speak(utterance);
    }
}
// ─── Toast ───────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 z-50 px-5 py-3.5 rounded-xl shadow-2xl text-white text-sm font-semibold
        flex items-center gap-3 transition-opacity duration-300
        ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    el.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
    document.body.appendChild(el);
    setTimeout(() => el.style.opacity = '0', 3000);
    setTimeout(() => el.remove(), 3400);
}

// ─── Auto-refresh every 30s ──────────────────────────────────────────────────
setInterval(updateQueueCounts, 30000);
</script>
@endpush

<style>
@keyframes fade-in { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:none; } }
.animate-fade-in { animation: fade-in .3s ease-out; }
.scrollbar-thin::-webkit-scrollbar { width: 5px; }
.scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
.scrollbar-thin::-webkit-scrollbar-thumb { background: #ff5252; border-radius: 4px; opacity: .3; }
</style>
@endsection
