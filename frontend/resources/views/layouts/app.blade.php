<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Abdelatif Lab Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- FontAwesome CDN --}}
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r flex flex-col shadow-md z-10">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-[#bc1622] mb-8 flex items-center gap-2">
                <i class="fas fa-vial"></i>
                <span>Abdelatif Lab</span>
            </h1>
            <nav class="space-y-4 text-sm text-gray-700 font-medium">
                <a href="{{ route('dashboard') }}" class="flex items-center hover:text-[#bc1622] transition-colors duration-200">
                    <i class="fas fa-home w-5 mr-2 text-[#bc1622]"></i> Dashboard
                </a>
                <a href="{{ route('patients.index') }}" class="flex items-center hover:text-[#bc1622] transition-colors duration-200">
                    <i class="fas fa-user-injured w-5 mr-2 text-[#bc1622]"></i> Patients
                </a>
                <a href="{{ route('doctors.index') }}" class="flex items-center hover:text-[#bc1622] transition-colors duration-200">
                    <i class="fas fa-user-md w-5 mr-2 text-[#bc1622]"></i> Doctors
                </a>
                <a href="{{ route('samples.index') }}" class="flex items-center hover:text-[#bc1622] transition-colors duration-200">
                    <i class="fas fa-flask w-5 mr-2 text-[#bc1622]"></i> Samples
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center hover:text-[#bc1622] transition-colors duration-200">
                    <i class="fas fa-file-medical-alt w-5 mr-2 text-[#bc1622]"></i> Reports
                </a>
            </nav>
        </div>
        <div class="mt-auto p-6 text-xs text-gray-400 select-none">© {{ now()->year }} Abdelatif Lab</div>
    </aside>

    <!-- Main Panel -->
    <div class="flex-1 flex flex-col bg-gray-50">

        <!-- Top Navbar -->
        <header class="flex items-center justify-between bg-white px-6 py-4 border-b shadow sticky top-0 z-20">
            <h2 class="text-lg font-semibold text-[#bc1622]">@yield('title', 'Dashboard')</h2>
            <div class="flex items-center space-x-4 text-gray-600 text-sm">
                <i class="fas fa-user-circle text-xl"></i>
                <span>Admin</span>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
