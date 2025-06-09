@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-gray-900 flex flex-col items-center justify-center text-white select-none">

    <header class="w-full max-w-5xl flex justify-between items-center mb-10">
        <h1 class="text-5xl font-extrabold tracking-wide">Queue Overview</h1>
        <button id="openFullscreenBtn" 
                class="bg-red-600 hover:bg-red-700 px-5 py-3 rounded-md text-lg font-semibold transition">
            Show Fullscreen
        </button>
    </header>

    {{-- Main page summary (optional, you can customize or remove) --}}
    <p class="max-w-3xl text-center text-gray-300 text-lg mb-16">
        Tap "Show Fullscreen" to view the detailed queue status for patients waiting.
    </p>

    {{-- Fullscreen Overlay --}}
    <div id="fullscreenOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-95 backdrop-blur-md z-50 hidden flex flex-col">
        <div class="flex justify-between items-center px-10 py-6 border-b border-red-700">
            <h2 class="text-6xl font-extrabold uppercase tracking-wide">File D'attente</h2>
            <button id="closeFullscreenBtn" 
                    class="bg-red-600 hover:bg-red-700 px-6 py-3 rounded-md text-xl font-semibold">
                Fermer
            </button>
        </div>

        <div class="flex-grow px-12 py-10 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-24">

            @foreach(['Reception' => 'reception', 'Prélèvement' => 'bloodDraw'] as $title => $queueKey)
            <section class="bg-gray-800 rounded-lg shadow-lg p-12 flex flex-col items-center space-y-12">
                <header class="border-b border-red-600 pb-4 w-full text-center">
                    <h3 class="text-5xl font-bold tracking-wide uppercase">{{ $title }}</h3>
                </header>

                <div class="w-full grid grid-cols-3 gap-12 text-center">

                    <div class="bg-red-700 rounded-lg py-12 px-8 flex flex-col justify-center items-center">
                        <h4 class="text-2xl font-semibold mb-4">Actuel</h4>
                        <p id="{{ $queueKey }}Current" class="text-8xl font-extrabold">-</p>
                        <p id="{{ $queueKey }}CurrentWait" class="mt-3 text-lg italic text-red-200"></p>
                        <p class="mt-1 text-sm text-red-300">Temps moyen: ~<span id="{{ $queueKey }}AvgWait">-</span> min</p>
                    </div>

                    <div class="bg-red-500 rounded-lg py-12 px-8 flex flex-col justify-center items-center">
                        <h4 class="text-2xl font-semibold mb-4">Suivant</h4>
                        <p id="{{ $queueKey }}Next" class="text-7xl font-extrabold">-</p>
                        <p id="{{ $queueKey }}NextWait" class="mt-3 text-lg italic text-red-100"></p>
                    </div>

                    <div class="bg-red-900 rounded-lg py-12 px-8 flex flex-col justify-center items-center">
                        <h4 class="text-2xl font-semibold mb-4">En Attente</h4>
                        <p id="{{ $queueKey }}Total" class="text-7xl font-extrabold">-</p>
                        <p id="{{ $queueKey }}EstWait" class="mt-3 text-lg italic text-red-200"></p>
                    </div>

                </div>
            </section>
            @endforeach

        </div>
    </div>
</div>

<script>
    const fastApiBase = "{{ env('FASTAPI_URL', 'http://localhost:8000') }}";
    const openBtn = document.getElementById('openFullscreenBtn');
    const closeBtn = document.getElementById('closeFullscreenBtn');
    const overlay = document.getElementById('fullscreenOverlay');

    openBtn.addEventListener('click', () => {
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // prevent background scroll
    });

    closeBtn.addEventListener('click', () => {
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !overlay.classList.contains('hidden')) {
            closeBtn.click();
        }
    });

    async function fetchQueueData() {
        try {
            const response = await fetch(`${fastApiBase}/queues/status`);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            document.getElementById('receptionCurrent').textContent = data.reception?.current ?? '-';
            document.getElementById('receptionNext').textContent = data.reception?.next ?? '-';
            document.getElementById('receptionTotal').textContent = data.reception?.total ?? '-';
            document.getElementById('receptionAvgWait').textContent = data.reception?.avg_wait_time ?? '-';
            document.getElementById('receptionEstWait').textContent = data.reception?.estimated_wait_time ?? '-';

            document.getElementById('bloodDrawCurrent').textContent = data.blood_draw?.current ?? '-';
            document.getElementById('bloodDrawNext').textContent = data.blood_draw?.next ?? '-';
            document.getElementById('bloodDrawTotal').textContent = data.blood_draw?.total ?? '-';
            document.getElementById('bloodDrawAvgWait').textContent = data.blood_draw?.avg_wait_time ?? '-';
            document.getElementById('bloodDrawEstWait').textContent = data.blood_draw?.estimated_wait_time ?? '-';

        } catch (error) {
            console.error('Failed to fetch queue data:', error);
        }
    }

    fetchQueueData();
    setInterval(fetchQueueData, 2500);
</script>

<style>
  /* Improve font smoothing and base font */
  body, #fullscreenOverlay {
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji';
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  /* Headers: consistent letter spacing and line height */
  header h1,
  #fullscreenOverlay h2,
  #fullscreenOverlay h3,
  #fullscreenOverlay h4 {
    letter-spacing: 0.05em;
    line-height: 1.2;
  }

  /* Paragraphs: improve readability */
  #fullscreenOverlay p {
    line-height: 1.6;
    font-weight: 500;
  }

  /* Large numbers: use a more readable font weight and line height */
  #fullscreenOverlay p.text-8xl,
  #fullscreenOverlay p.text-7xl {
    font-weight: 900;
    line-height: 1;
    font-feature-settings: "tnum";
    font-variant-numeric: tabular-nums;
  }

  /* Button fonts */
  #openFullscreenBtn,
  #closeFullscreenBtn {
    font-family: 'Inter', system-ui, sans-serif;
    font-weight: 600;
  }

  /* Fix overlay visibility transition */
  #fullscreenOverlay {
    transition: opacity 0.3s ease, visibility 0.3s ease;
    opacity: 0;
    visibility: hidden;
  }
  #fullscreenOverlay.flex {
    opacity: 1;
    visibility: visible;
  }
 #fullscreenOverlay .grid > div {  
    text-align: center; /* Override parent */  
  }
  /* Fine-tune vertical alignment of text */  
  #fullscreenOverlay #bloodDrawNext {  
    margin-top: -0.25em; /* Fine-tune vertical position */  
    display: block; /* Ensure it respects margin */  
  } 
  #fullscreenOverlay .bg-red-500 h4 {  
    margin-bottom: 0.6em; /* Adjust spacing */  
  }
</style>
@endsection
