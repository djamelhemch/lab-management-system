@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-gray-900 flex flex-col items-center justify-center text-white select-none">
    {{-- Control Header --}}
    <header class="w-full max-w-5xl flex justify-between items-center mb-10 bg-gray-900 text-white p-4 rounded-md">
        <h1 class="text-5xl font-extrabold tracking-wide">Voir la file d'attente</h1>
        <button id="openFullscreenBtn" class="bg-red-600 hover:bg-red-700 px-5 py-3 rounded-md text-lg font-semibold transition">
            Plein écran
        </button>
    </header>

    {{-- Main page summary --}}
    <p class="max-w-3xl text-center text-gray-300 text-lg mb-16">
        Clicker sur "Plein écran" pour voir l'écran de file d'attente.
    </p>

    {{-- Fullscreen Waiting Room --}}
    
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
                <p id="currentDate" class="text-3xl font-light nexa-light text-gray-900">Mercredi 15 Mai 2024</p>
                <p id="currentTime" class="text-5xl font-extrabold nexa-bold text-black text-center">12:00</p>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-grow flex items-center px-10 py-6 gap-16">
            {{-- Left: Video --}}
            <div class="flex-grow mr-16 max-w-[1380px]">
                <video autoplay loop muted playsinline class="lab-video">
                    <source src="{{ $videoSrc }}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </div>

            {{-- Right: Ticket Display --}}
            <div class="flex flex-col gap-14">
                {{-- Poste 01 - Réception --}}
                <div class="ticket-display flex items-center justify-center">
                    <div class="ticket-inner-circle flex flex-col items-center justify-center">
                        <p class="ticket-title nexa-bold">Réception</p>
                        <p class="ticket-label nexa-light">Ticket</p>
                        <p id="receptionCurrent" class="ticket-number nexa-bold">-</p>
                    </div>
                </div>

                {{-- Poste 02 - Prise de sang --}}
                <div class="ticket-display flex items-center justify-center">
                    <div class="ticket-inner-circle flex flex-col items-center justify-center">
                        <p class="ticket-title nexa-bold">Prise de sang</p>
                        <p class="ticket-label nexa-light">Ticket</p>
                        <p id="bloodDrawCurrent" class="ticket-number nexa-bold">-</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Marquee --}}
        <div class="marquee-container bg-red-500 text-white py-4">
            <div class="marquee-content text-xl font-semibold whitespace-nowrap">
                {{ $marqueeText }}
            </div>
        </div>

        {{-- Close Button --}}
        <button id="closeFullscreenBtn" class="fullscreen-close-btn">
            <span class="close_btn">×</span>
        </button>
    </div>
</div>
<script>
    const fastApiBase = "https://lab-api.hemchracing.com/queues";
    const openBtn = document.getElementById('openFullscreenBtn');
    const closeBtn = document.getElementById('closeFullscreenBtn');
    const overlay = document.getElementById('fullscreenOverlay');
    let eventSource = null;
    let reconnectAttempts = 0;
    let sseFailures = 0;
    let heartbeatInterval = null;
    let pollInterval = null;
    let updateCount = 0;
    let lastTickets = { reception: null, blood_draw: null };

    // Open fullscreen
    openBtn.addEventListener('click', () => {
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        connectSSE();
        updateDebug('SSE Starting...');
    });

    // Close fullscreen
    closeBtn.addEventListener('click', () => {
        disconnectEverything();
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    });

    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !overlay.classList.contains('hidden')) {
            closeBtn.click();
        }
    });

    // Toggle debug overlay on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target.id === 'fullscreenOverlay') {
            const debugOverlay = document.getElementById('debugOverlay');
            if (debugOverlay) debugOverlay.classList.toggle('hidden');
        }
    });

    // ✅ INDUSTRIAL-STRENGTH SSE + POLLING FALLBACK
    function connectSSE() {
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }

        updateDebug('🔗 Connecting SSE...');
        eventSource = new EventSource(`${fastApiBase}/status/stream?nocache=${Date.now()}`);

        eventSource.onopen = () => {
            console.log('✅ SSE Connected - Live updates active');
            updateDebug('✅ SSE Connected');
            reconnectAttempts = 0;
            sseFailures = 0;
            startHeartbeat();
        };

        eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                console.log('📨 SSE LIVE:', data);
                updateDebug(`📨 ${JSON.stringify(data.reception.current_ticket)} / ${JSON.stringify(data.blood_draw.current_ticket)}`);
                processTicketUpdate(data);
                updateCount++;
            } catch (e) {
                console.error('❌ SSE Parse error:', e, event.data);
            }
        };

        eventSource.onerror = (err) => {
            console.log('⚠️ SSE Error');
            eventSource.close();
            eventSource = null;
            sseFailures++;
            updateDebug(`⚠️ SSE Error (${sseFailures}/5)`);
            
            if (sseFailures >= 5) {
                console.log('🔄 SSE failed → Polling fallback');
                startPollingFallback();
            } else {
                // Progressive reconnect
                setTimeout(connectSSE, 2000 + (reconnectAttempts * 1000));
                reconnectAttempts++;
            }
        };
    }

    function startHeartbeat() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        heartbeatInterval = setInterval(() => {
            console.log('💓 SSE Heartbeat');
        }, 30000);
    }

    function startPollingFallback() {
        updateDebug('🔄 Polling fallback (3s)');
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            try {
                const response = await fetch(`${fastApiBase}/status`);
                if (response.ok) {
                    const data = await response.json();
                    console.log('🔄 Poll:', data);
                    processTicketUpdate(data);
                }
            } catch (e) {
                console.error('Poll failed:', e);
            }
        }, 3000);
    }

    function disconnectEverything() {
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }
        if (heartbeatInterval) {
            clearInterval(heartbeatInterval);
            heartbeatInterval = null;
        }
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
        console.log('❌ All connections closed');
        updateDebug('Disconnected');
    }

    // ✅ CORE: Process ticket updates with change detection + animation
    function processTicketUpdate(data) {
        const receptionEl = document.getElementById('receptionCurrent');
        const bloodDrawEl = document.getElementById('bloodDrawCurrent');

        const recTicket = data.reception?.current_ticket;
        const bdTicket = data.blood_draw?.current_ticket;

        const newRecText = recTicket != null ? `N°${String(recTicket).padStart(2, '0')}` : '-';
        const newBdText = bdTicket != null ? `N°${String(bdTicket).padStart(2, '0')}` : '-';

        // Animate ONLY if changed
        if (receptionEl.textContent !== newRecText) {
            console.log('🎫 RECEPTION NEW:', newRecText);
            animateTicket(receptionEl, newRecText, '#A61731', 'Réception');
        }
        if (bloodDrawEl.textContent !== newBdText) {
            console.log('🎫 BLOOD DRAW NEW:', newBdText);
            animateTicket(bloodDrawEl, newBdText, '#FF4444', 'Prise de sang');
        }

        // Update without animation if same
        receptionEl.textContent = newRecText;
        bloodDrawEl.textContent = newBdText;

        lastTickets = { reception: recTicket, blood_draw: bdTicket };
    }

    // 🎨 INDUSTRIAL ANIMATION
    function animateTicket(element, newText, accentColor, stationName) {
        const container = element.parentElement.parentElement;
        const innerCircle = element.parentElement;

        // 1. Flash border + background
        container.style.borderColor = accentColor;
        container.style.boxShadow = `0 0 30px ${accentColor}`;
        innerCircle.style.background = `radial-gradient(circle, ${accentColor} 20%, rgba(226,69,102,0.8) 70%)`;

        // 2. Explosive scale + glow
        element.style.transform = 'scale(1.4)';
        element.style.textShadow = `0 0 25px rgba(255,255,255,1), 0 0 40px ${accentColor}`;
        element.style.transition = 'all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';

        // 3. Update text mid-animation
        requestAnimationFrame(() => {
            element.textContent = newText;
        });

        // 4. Reset after 1.2s
        setTimeout(() => {
            element.style.transform = 'scale(1)';
            element.style.textShadow = 'none';
            container.style.borderColor = '#A61731';
            container.style.boxShadow = '0 10px 15px rgba(0,0,0,0.2)';
            innerCircle.style.background = 'linear-gradient(-230deg, rgba(255,255,255,1) 0%, rgba(226,69,102,1) 50%)';
        }, 1200);
    }

    // 🐛 DEBUG OVERLAY SUPPORT (add this HTML element)
    function updateDebug(message) {
        const statusEl = document.getElementById('sseStatus');
        const lastDataEl = document.getElementById('lastData');
        const countEl = document.getElementById('updateCount');
        
        if (statusEl) statusEl.textContent = message;
        if (countEl) countEl.textContent = `Updates: ${updateCount}`;
    }

    // ⏰ Date/Time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDate').textContent = 
            now.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('currentTime').textContent = 
            now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // 🧹 Cleanup
    window.addEventListener('beforeunload', disconnectEverything);
    window.addEventListener('pagehide', disconnectEverything);
</script>
<style>
.logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (min-width: 768px) {
    .logo-container { justify-content: flex-start; }
}
.logo-img { height: 4.5rem; width: auto; }

.doctor-name {
    font-family: 'Nexa', sans-serif;
    font-weight: 900;
    margin-bottom: 0.25rem;
    font-size: 2rem;
    color: #000;
}
.doctor-subname {
    font-family: 'Nexa', sans-serif;
    font-weight: 700;
    font-size: 1.85rem;
    color: #000;
}
.doctor-specialty {
    font-family: 'Nexa', sans-serif;
    font-size: 1.25rem;
    color: #0e0d0d;
    font-weight: 600;
}

.lab-video {
    border: 6px solid #A61731;
    border-radius: 1rem;
    box-shadow: 0 10px 15px rgba(0,0,0,0.2);
    object-fit: cover;
    width: 100%;
    max-height: 628px;
}

.ticket-display {
    width: 288px;
    height: 288px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 4px solid #A61731;
    background-color: #fff;
}
.ticket-inner-circle {
    width: 240px;
    height: 240px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(-230deg, rgba(255,255,255,1) 0%, rgba(226,69,102,1) 50%);
    color: white;
}
.ticket-title { font-size: 30px; font-weight: bold; margin-bottom: -38px; color: white; }
.ticket-label { font-size: 24px; margin-bottom: -31px; margin-top: 44px; color: white; }
.ticket-number { font-size: 70px; font-weight: 800; margin-bottom: -8px; color: white; }

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
.nexa-light { font-family: 'Nexa', sans-serif; font-weight: 300; }
.nexa-bold { font-family: 'Nexa', sans-serif; font-weight: 700; }

.marquee-container { overflow: hidden; position: relative; }
.marquee-content { 
    display: inline-block;
    padding-left: 100%;
    animation: marquee 25s linear infinite;
}
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

.fullscreen-close-btn {
    position: fixed;
    top: calc(100vh - 58px);
    right: 0.8rem;
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.3);
    color: rgba(55, 65, 81, 1);
    backdrop-filter: blur(8px);
    border-radius: 9999px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border: 1px solid rgba(156, 163, 175, 0.5);
    transition: all 0.2s ease;
    z-index: 50;
    cursor: pointer;
    font-size: 1.25rem;
    line-height: 1;
}
.fullscreen-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.4);
    color: rgba(31, 41, 55, 1);
    border-color: rgba(156, 163, 175, 0.8);
}
.fullscreen-close-btn .close_btn {
    position: fixed;
    top: -3px;
    font-size: 1.5rem;
}
</style>
@endsection