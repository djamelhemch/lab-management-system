@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-6 lg:px-20 flex flex-col lg:flex-row gap-12">

    {{-- Left: Queues --}}
    <div class="flex-1 flex flex-col gap-10">

        {{-- Reception Queue --}}
        <section class="bg-white rounded-lg shadow-lg flex flex-col">
            <header class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 flex flex-col sm:flex-row justify-between items-center z-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Reception Queue</h2>
                    <p class="text-sm text-gray-600 mt-1">Waiting: {{ count($receptionQueue) }}</p>
                </div>
                <form id="moveNextForm" method="POST" action="{{ route('queues.moveNext') }}" class="mt-4 sm:mt-0">
                    @csrf
                    <button type="submit" id="moveNextBtn"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-12 rounded-lg shadow-lg transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-400 disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ count($receptionQueue) === 0 ? 'disabled' : '' }}>
                        Move Next &#8594;
                    </button>
                </form>
                <audio id="notification-sound" src="/audio/notify.mp3" preload="auto"></audio>
            </header>

            <ul class="divide-y divide-gray-200 max-h-[36rem] overflow-y-auto px-6 py-4">
                @forelse($receptionQueue as $item)
                    <li class="flex justify-between items-center py-3 hover:bg-gray-50 rounded-md transition cursor-default">
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-red-100 text-red-800 font-semibold select-none">
                                {{ $item['position'] }}
                            </span>
                            <div>
                                <h3 class="text-md font-medium text-gray-900 select-text">{{ $patients[$item['patient_id']] ?? 'Patient #'.$item['patient_id'] }}</h3>
                                @if($item['quotation_id'])
                                    <p class="text-xs text-gray-500 select-text">Quotation: <span class="font-semibold">#{{ $item['quotation_id'] }}</span></p>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" onsubmit="return confirm('Remove this patient?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50 transition" aria-label="Remove patient">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="py-20 text-center text-gray-400 select-none">
                        <i class="fas fa-clock fa-3x mb-4"></i>
                        <p class="text-lg">No patients in reception queue.</p>
                    </li>
                @endforelse
            </ul>
        </section>

        {{-- Blood Draw Queue --}}
        <section class="bg-white rounded-lg shadow-lg flex flex-col">
            <header class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <h2 class="text-2xl font-bold text-gray-900">Blood Draw Queue</h2>
                <p class="text-sm text-gray-600 mt-1">Waiting: {{ count($bloodDrawQueue) }}</p>
            </header>

            <ul class="divide-y divide-gray-200 max-h-[36rem] overflow-y-auto px-6 py-4">
                @forelse($bloodDrawQueue as $item)
                    <li class="flex justify-between items-center py-3 hover:bg-gray-50 rounded-md transition cursor-default">
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-red-100 text-red-800 font-semibold select-none">
                                {{ $item['position'] }}
                            </span>
                            <div>
                                <h3 class="text-md font-medium text-gray-900 select-text">{{ $patients[$item['patient_id']] ?? 'Patient #'.$item['patient_id'] }}</h3>
                                @if($item['quotation_id'])
                                    <p class="text-xs text-gray-500 select-text">Quotation: <span class="font-semibold">#{{ $item['quotation_id'] }}</span></p>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" onsubmit="return confirm('Remove this patient?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50 transition" aria-label="Remove patient">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="py-20 text-center text-gray-400 select-none">
                        <i class="fas fa-clock fa-3x mb-4"></i>
                        <p class="text-lg">No patients in blood draw queue.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- Right: Add to Queue Form --}}
    <aside class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg sticky top-10 self-start">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Add Patient to Queue</h2>
        <form method="POST" action="{{ route('queues.store') }}" class="space-y-6">
            @csrf
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                <select name="patient_id" id="patient_id" required
                    class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-3">
                    <option value="">Select Patient</option>
                    @foreach($patients as $id => $name)
                        <option value="{{ $id }}" {{ old('patient_id')==$id?'selected':'' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="quotation_id" class="block text-sm font-medium text-gray-700 mb-2">Quotation (optional)</label>
                <input type="number" name="quotation_id" id="quotation_id" value="{{ old('quotation_id') }}" placeholder="#ID"
                    class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-3" />
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Queue Type</label>
                <select name="type" id="type" required
                    class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-3">
                    <option value="">Select Type</option>
                    <option value="reception" {{ old('type')=='reception'?'selected':'' }}>Reception</option>
                    <option value="blood_draw" {{ old('type')=='blood_draw'?'selected':'' }}>Blood Draw</option>
                </select>
            </div>
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-4 rounded-lg shadow-lg transition focus:outline-none focus:ring-4 focus:ring-red-400">
                <i class="fas fa-plus mr-2"></i> Add to Queue
            </button>
        </form>
    </aside>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('moveNextForm');
    const audio = document.getElementById('notification-sound');
    const moveNextBtn = document.getElementById('moveNextBtn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (moveNextBtn.disabled) return;

        moveNextBtn.disabled = true;
        moveNextBtn.textContent = 'Processing...';

        audio.currentTime = 0;
        audio.play().then(() => {
            audio.onended = () => {
                form.submit();
            };
        }).catch((err) => {
            console.warn('Audio playback failed or blocked:', err);
            form.submit();
        });
    });
});
</script>

@if(session('next_patient'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nextPatient = @json(session('next_patient'));
    const patients = @json($patients);

    const patientName = patients[nextPatient.patient_id] || ('Patient numéro ' + nextPatient.patient_id);
    const position = nextPatient.position || '';
    let announcement = `Nous appelons le numéro. `;
    if (position) {
        announcement += `${position}. Veuillez vous présenter à la prise de sang. Merci.`;
    } else {
        announcement += `${patientName}, veuillez vous présenter à la salle de prélèvement pour la prise de sang. Merci.`;
    }

    function speak(text) {
        return new Promise((resolve) => {
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'fr-FR';
            utter.rate = 0.92;
            utter.pitch = 1.05;

            const voices = window.speechSynthesis.getVoices();
            utter.voice = voices.find(v => v.lang === 'fr-FR') || null;

            utter.onend = resolve;
            window.speechSynthesis.speak(utter);
        });
    }

    speak(announcement);
});
</script>
@endif