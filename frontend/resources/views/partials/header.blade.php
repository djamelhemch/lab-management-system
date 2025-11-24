<header class="bg-white px-6 py-4 flex justify-between items-center sticky">
    <div class="flex items-center">
        <button id="openSidebar" class="md:hidden text-gray-600 focus:outline-none mr-4">
            <i class="fas fa-bars text-xl"></i>
        </button>
   
    </div>
    <div class="flex items-center space-x-4">
        @if(isset($authUser))
            <p>{{ $authUser['username'] }} ({{ $authUser['role'] }})</p>
        @endif

        {{-- Use the uploaded photo or fallback to placeholder --}}
<a href="{{ route('profiles.show', $authUser['id']) }}">
    <img src="{{ $authUser['photo_url'] ?? 'https://placehold.co/32x32' }}" 
         alt="Avatar" 
         class="rounded-full border border-gray-200 shadow-sm w-8 h-8 object-cover cursor-pointer">
</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition">Déconnexion</button>
        </form>
    </div>
</header>
