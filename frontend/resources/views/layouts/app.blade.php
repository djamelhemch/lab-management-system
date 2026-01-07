<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Abdelatif Lab')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        body.light {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-accent: #f1f3f5;
            --text-primary: #212529;
            --text-secondary: #495057;
            --accent-color: #ff5252;
            --border-color: #e9ecef;
            --hover-bg: rgba(255, 82, 82, 0.1);
        }

        body.dark {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-accent: #2d2d2d;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --accent-color: #ff6b6b;
            --border-color: #343a40;
            --hover-bg: rgba(255, 107, 107, 0.1);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        a { color: var(--accent-color); transition: color 0.2s ease; }
        button { background-color: var(--accent-color); color: white; transition: background-color 0.2s ease; }

        .sidebar-link { color: var(--text-secondary); background-color: transparent; }
        .sidebar-link:hover { color: var(--accent-color); background-color: var(--hover-bg); }
        .sidebar-link.active { color: var(--accent-color); background-color: var(--hover-bg); font-weight: 600; }

        table, input, select, textarea {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        /* Smooth scrolling for overflow containers */
        #sidebar, #sidebar nav {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        /* Custom scrollbar styling */
        #sidebar nav::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar nav::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        body.dark #sidebar nav::-webkit-scrollbar-thumb {
            background: #4a5568;
        }
    </style>
</head>

<body class="{{ session('theme', 'light') }}">
    <div class="flex h-screen overflow-hidden">
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('partials.header')

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40 md:hidden"></div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
        }

        document.getElementById('openSidebar')?.addEventListener('click', openSidebar);
        document.getElementById('closeSidebar')?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    </script>

    <script>
        window.apiUrl = "{{ env('FASTAPI_URL', 'http://127.0.0.1:8000') }}";
    </script>

    @stack('scripts')
</body>
</html>