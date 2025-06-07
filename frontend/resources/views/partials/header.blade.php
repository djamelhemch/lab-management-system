<header class="bg-white shadow px-6 py-4 flex justify-between items-center sticky top-0 z-30">
    <div class="flex items-center">
        <button id="openSidebar" class="md:hidden text-gray-600 focus:outline-none mr-4">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h1 class="text-2xl font-bold text-red-600 tracking-tight">Dashboard</h1>
    </div>
    <div class="flex items-center space-x-4">
        @if(isset($authUser))
            <p>Welcome, {{ $authUser['username'] }} ({{ $authUser['role'] }})</p>
        @endif
        <img src="https://placehold.co/32x32" alt="Avatar" class="rounded-full border border-gray-200 shadow-sm">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition">Logout</button>
        </form>
    </div>
</header>