@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-gray-900 flex flex-col items-center justify-center text-white select-none">

    <header class="w-full max-w-5xl flex justify-between items-center mb-10 bg-gray-900 text-white p-4 rounded-md">
        <h1 class="text-5xl font-extrabold tracking-wide">Voir la fille d'attente</h1>
        <button id="openFullscreenBtn" class="bg-red-600 hover:bg-red-700 px-5 py-3 rounded-md text-lg font-semibold transition">
            Plein écran
        </button>
    </header>

    {{-- Main page summary (optional, you can customize or remove) --}}
    <p class="max-w-3xl text-center text-gray-300 text-lg mb-16">
        Clicker sur "Plein écran" pour voir l'écran de file d'attente.
    </p>

   <div id="fullscreenOverlay" class="fixed inset-0 bg-white z-50 hidden flex flex-col text-gray-900 select-none">

    {{-- Header --}}
    <div class="flex justify-between items-start px-10 pt-8">
        <div>
            <div class="logo-container">
                <img src="/images/logo_lab.PNG" alt="Abdelatif Lab" class="logo-img">
            </div>
            <div class="doctor-info">
                <p class="doctor-name">
                    Dr N.HAKIKI <span class="doctor-subname">ep.BOUACHRIA</span>
                </p>
                <p class="doctor-specialty">Médecin spécialiste en Hématologie</p>
            </div>
        </div>
        
        <div class="flex flex-col items-center mb-8">
            <!-- Date -->
            <p id="currentDate" class="text-3xl font-light nexa-light text-gray-900">Mercredi 15 Mai 2024</p>
            <!-- Time -->
            <p id="currentTime" class="text-5xl font-extrabold nexa-bold text-black text-center">12:00</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-grow flex items-center px-10 py-6 gap-16">
        
        {{-- Left: Video --}}
        <div class="flex-grow mr-16 max-w-[1380px]">
            <video autoplay loop muted playsinline class="lab-video">
                <source src="/videos/lab_video.mp4" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
        </div>

        {{-- Right: Ticket display --}}
       <div class="flex flex-col gap-14">
            <!-- Poste 01 -->
            <div class="ticket-display flex items-center justify-center">
                <!-- Inner Circle -->
                <div class="ticket-inner-circle flex flex-col items-center justify-center">
                    <p class="ticket-title nexa-bold">Réception</p>
                    <p class="ticket-label nexa-light">Ticket</p>
                    <p id="receptionCurrent" class="ticket-number nexa-bold"></p>
                </div>
            </div>

            <!-- Poste 02 -->
            <div class="ticket-display flex items-center justify-center">
                <!-- Inner Circle -->
                <div class="ticket-inner-circle flex flex-col items-center justify-center">
                    <p class="ticket-title nexa-bold">Prise de sang</p>
                    <p class="ticket-label nexa-light">Ticket</p>
                    <p id="bloodDrawCurrent" class="ticket-number nexa-bold"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="marquee-container bg-red-500 text-white py-4">
        <div class="marquee-content text-xl font-semibold whitespace-nowrap">
           L'ÉTABLISSEMENT "ABDELATIF LAB" LABORATOIRE D'ANALYSES DE SANG CONVENTIONNÉ AVEC LE LABORATOIRE CERBA EN FRANCE VOUS SOUHAITE LA BIENVENUE, LE LABORATOIRE EST OUVERT DU SAMEDI AU JEUDI DE 7H30 à 16H30. 
        </div>
    </div>
    
    {{-- Close Button --}}
    <button id="closeFullscreenBtn" class="fullscreen-close-btn">
        <span class="close_btn">×</span>
    </button>
</div>
</div>

<script>
    const fastApiBase = "{{ env('FASTAPI_URL', 'http://localhost:8000') }}";
    const openBtn = document.getElementById('openFullscreenBtn');
    const closeBtn = document.getElementById('closeFullscreenBtn');
    const overlay = document.getElementById('fullscreenOverlay');

    // Open fullscreen
    openBtn.addEventListener('click', () => {
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    // Close fullscreen
    closeBtn.addEventListener('click', () => {
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    });

    // Escape key closes fullscreen
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !overlay.classList.contains('hidden')) {
            closeBtn.click();
        }
    });

    // Update date & time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDate').textContent =
            now.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('currentTime').textContent =
            now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    // Fetch queue data
    async function fetchQueueData() {
        try {
            const res = await fetch(`${fastApiBase}/queues/status`);
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();
        document.getElementById('receptionCurrent').textContent =
            data.reception?.current != null ? `N°${String(data.reception.current).padStart(2, '0')}` : '-';

        document.getElementById('bloodDrawCurrent').textContent =
            data.blood_draw?.current != null ? `N°${String(data.blood_draw.current).padStart(2, '0')}` : '-';
        } catch (err) {
            console.error(err);
        }
    }

    // Intervals for updates
    setInterval(updateDateTime, 1000);
    updateDateTime();

    fetchQueueData();
    setInterval(fetchQueueData, 2500);
</script>

<style>

  /* Logo container */
.logo-container {
    display: flex;
    align-items: center;
    justify-content: center; /* center on small screens */
}

@media (min-width: 768px) {
    .logo-container {
        justify-content: flex-start; /* align left on medium+ screens */
    }
}

/* Logo image */
.logo-img {
    height: 4.5rem; /* 80px */
    width: auto;  /* maintain aspect ratio */
}

/* Doctor name */
.doctor-name {
    font-family: 'Nexa', sans-serif;
    font-weight: 900;        /* extra bold */
    margin-bottom: 0.25rem;
    font-size: 2rem;  
    color: #000;             /* dark color */
}

/* Subname (ep.BOUACHRIA) */
.doctor-subname {
    font-family: 'Nexa', sans-serif;
    font-weight: 700;        /* normal weight */
    font-size: 1.85rem;  
    color: #000;     /* slightly smaller */
}

/* Specialty */
.doctor-specialty {
    font-family: 'Nexa', sans-serif;
    font-size: 1.25rem;      /* larger than default */
    color: #0e0d0dff;             /* gray */
    font-weight: 600; /* semi-bold */
}

.lab-video {
    border: 6px solid #A61731; /* existing border */
    border-radius: 1rem; /* matches rounded-2xl */
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); /* shadow-xl */
    object-fit: cover;
    width: 100%;
    max-height: 628px;
    }

.ticket-display {
    width: 288px; /* approx 72 x 4rem */
    height: 288px; /* same as above */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 4px solid #A61731;
    background-color: #fff;
    
}

.ticket-title {
    font-size: 30px;
    font-weight: bold;
    margin-bottom: -38px;
    color: white; /* Updated color */
}

.ticket-label {
    font-size: 24px;
    margin-bottom: -31px;
    margin-top: 44px; /* Adjust vertical alignment */
    color: white; /* Updated color */
}

.ticket-number {
    font-size: 70px;
    font-weight: 800;
    margin-bottom: -8px;
    color: white; /* Updated color */
}

/* Optional: if you want a gradient background inside the inner circle */
.ticket-inner-circle {
    width: 240px; /* approx 60 x 4rem */ 
    height: 240px; 
    display:  flex; 
    flex-direction: column; 
    align-items: center; 
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(-230deg, rgba(255,255,255,1) 0%, rgba(226,69,102,1) 50%);
    color: white; 
}
@font-face {
  font-family: 'Nexa';
  src: url('/fonts/NexaLight.otf') format('opentype');
  font-weight: 300;
  font-style: normal;
}

@font-face {
  font-family: 'Nexa';
  src: url('/fonts/NexaBold.otf') format('opentype');
  font-weight: 700;
  font-style: normal;
}

.marquee-container {
    overflow: hidden;       /* hide overflowing text */
    position: relative;
}

.marquee-content {
    display: inline-block;
    padding-left: 100%;     /* start offscreen */
    animation: marquee 25s linear infinite; /* adjust duration for speed */
}

@keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* Utility classes */
.nexa-light {
  font-family: 'Nexa', sans-serif;
  font-weight: 300;
}

.nexa-bold {
  font-family: 'Nexa', sans-serif;
  font-weight: 700;
}

.fullscreen-close-btn {
    position: fixed;
    bottom: 56rem; /* bottom-6 */
    right: 0.8rem; /* right-6 */
    width: 1.5rem; /* w-10 */
    height: 1.5rem; /* h-10 */
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.3); /* bg-white/30 */
    color: rgba(55, 65, 81, 1); /* text-gray-700 */
    backdrop-filter: blur(8px); /* backdrop-blur-sm */
    border-radius: 9999px; /* rounded-full */
    box-shadow: 0 1px 2px rgba(0,0,0,0.05); /* shadow-sm */
    border: 1px solid rgba(156, 163, 175, 0.5); /* border-gray-300/50 */
    transition: all 0.2s ease; /* transition-all duration-200 */
    z-index: 50;
    cursor: pointer;
    font-size: 1.25rem; /* text-xl */
    line-height: 1; /* leading-none */
}

.fullscreen-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.4); /* hover:bg-white/40 */
    color: rgba(31, 41, 55, 1); /* hover:text-gray-900 */
    border-color: rgba(156, 163, 175, 0.8); /* hover:border-gray-400/50 */
}
.fullscreen-close-btn .close_btn {
    position: relative; /* allow offset */
    top: -3px;   /* move up */
    font-size: 1.5rem; /* optional: make it bigger */
}
</style>
@endsection
