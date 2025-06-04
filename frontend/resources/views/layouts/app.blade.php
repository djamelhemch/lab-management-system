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
    <style>
        .sidebar-link { @apply flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 text-gray-700 hover:bg-red-50 hover:text-red-600; }
        .sidebar-link.active { @apply bg-red-100 text-red-700 font-semibold; }
        .stat-card { @apply bg-white rounded-xl shadow p-6 transition hover:shadow-lg; }
    </style>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-50 text-gray-900">
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
    @stack('scripts')
</body>
</html>