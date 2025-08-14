<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Abdelatif Lab')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
    /* Light theme (default) */
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

    /* Dark theme */
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

    /* Base styles */
    body {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    a {
        color: var(--accent-color);
        transition: color 0.2s ease;
    }

    button {
        background-color: var(--accent-color);
        color: white;
        transition: background-color 0.2s ease;
    }

    /* Sidebar links */
    .sidebar-link {
        @apply flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200;
    }
    
    .sidebar-link {
        color: var(--text-secondary);
        background-color: transparent;
    }
    
    .sidebar-link:hover {
        color: var(--accent-color);
        background-color: var(--hover-bg);
    }
    
    .sidebar-link.active {
        color: var(--accent-color);
        background-color: var(--hover-bg);
        font-weight: 600;
    }

    /* Stat cards */
    .stat-card {
        @apply rounded-xl shadow p-6 transition hover:shadow-lg;
        background-color: var(--bg-secondary);
    }

    /* Header */
    header {
        @apply flex justify-between items-center px-6 py-4 sticky top-0 z-30 shadow;
        background-color: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
    }

    /* Tables & Forms */
    table, input, select, textarea {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        border-color: var(--border-color);
    }

    /* Additional dark mode specific adjustments */
    body.dark input::placeholder {
        color: var(--text-secondary);
        opacity: 0.7;
    }

    /* Consistent accent colors */
    .text-red-600 {
        color: var(--accent-color);
    }
    
    .bg-red-600 {
        background-color: var(--accent-color);
    }
</style>

    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="{{ session('theme', 'light') }}">
    <div class="flex h-screen overflow-hidden">
        @include('partials.sidebar')
        <div class="flex-1 flex flex-col min-w-0">
            @include('partials.header')
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    <div id="overlay" class="fixed inset-0 bg-black opacity-40 hidden z-40 md:hidden"></div>
    <script>
        document.getElementById('openSidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar')?.classList.add('open');
            document.getElementById('overlay')?.classList.remove('hidden');
        });
        document.getElementById('closeSidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar')?.classList.remove('open');
            document.getElementById('overlay')?.classList.add('hidden');
        });
        document.getElementById('overlay')?.addEventListener('click', () => {
            document.getElementById('sidebar')?.classList.remove('open');
            document.getElementById('overlay')?.classList.add('hidden');
        });
    </script>
    <script>
        const themeSelect = document.querySelector('select[name="theme"]');
        themeSelect?.addEventListener('change', (e) => {
            const theme = e.target.value;
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(theme);

            // Optionally, persist immediately via AJAX
            fetch("{{ route('profiles.update', $profile['user_id'] ?? 0) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ theme }),
            }).then(res => res.json())
            .then(data => console.log("Theme updated", data))
            .catch(err => console.error(err));
        });
    </script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {},
            },
        }
    </script>
    @stack('scripts')
</body>
</html>