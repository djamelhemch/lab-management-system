@php
    $user = session('user'); // FastAPI authenticated user
@endphp
<div
  id="sidebar"
  class="bg-white w-64 fixed inset-y-0 left-0 z-50 shadow-2xl
         transform -translate-x-full transition duration-200 ease-in-out
         md:relative md:translate-x-0 md:shadow-none
         flex flex-col h-screen overflow-hidden"
>
  <!-- Top (non-scrolling) -->
  <div class="flex items-center justify-between px-4 py-4 shrink-0 border-b border-gray-200">
    <a href="#" class="logo-link">
      <img src="{{ $logoUrl }}" alt="Abdelatif Lab" class="logo-img">
    </a>
    <button id="closeSidebar" class="md:hidden text-gray-600 focus:outline-none">
      <i class="fas fa-times text-xl"></i>
    </button>
  </div>

  <!-- Scrollable navigation -->
  <nav class="flex-1 overflow-y-auto px-2 py-4">
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} {{ request()->routeIs('dashboard') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-tachometer-alt"></i> <span class="ml-2">Tableau de bord</span>
    </a>
    <a href="{{ route('hub.index') }}"
    class="sidebar-link {{ request()->routeIs('hub') ? 'active' : '' }} 
    {{ request()->routeIs('hub') ? 'bg-indigo-100 text-indigo-700 font-bold shadow' : 'text-gray-700 hover:bg-indigo-50' }} 
    flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        
        <i class="fas fa-bolt"></i>
        <span class="ml-2">Centre d’actions</span>
    </a>
    <a href="{{ route('patients.index') }}" class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }} {{ request()->routeIs('patients.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-user-injured"></i> <span class="ml-2">Patients</span>
    </a>
    <a href="{{ route('doctors.index') }}" class="sidebar-link {{ request()->routeIs('doctors.*') ? 'active' : '' }} {{ request()->routeIs('doctors.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-user-md"></i> <span class="ml-2">Médecins</span>
    </a>
    <a href="{{ route('samples.index') }}" class="sidebar-link {{ request()->routeIs('samples.*') ? 'active' : '' }} {{ request()->routeIs('samples.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-vial"></i> <span class="ml-2">Échantillons</span>
    </a>

    <a href="{{ route('lab-results.index') }}" class="sidebar-link {{ request()->routeIs('lab-results.*') ? 'active bg-blue-100 text-blue-700 font-bold shadow' : 'text-gray-700 hover:bg-blue-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-vials"></i> 
        <span class="ml-2">Résultats</span>
    </a>

    <a href="{{ route('analyses.index') }}" class="sidebar-link {{ request()->routeIs('analyses.*') ? 'active' : '' }} {{ request()->routeIs('analyses.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">  
        <i class="fas fa-flask"></i> <span class="ml-2">Analyses</span>  
    </a>

    <a href="{{ route('lab-devices.index') }}" 
    class="sidebar-link {{ request()->routeIs('lab-devices.*') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-microscope"></i> 
        <span class="ml-2">Lab Devices</span>
    </a>

    <a href="{{ route('statistics.index') }}" 
    class="sidebar-link {{ request()->routeIs('statistics.*') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-chart-line"></i> 
        <span class="ml-2">Statistiques</span>
    </a>

    <a href="{{ route('quotations.index') }}" class="sidebar-link {{ request()->routeIs('quotations.*') ? 'active' : '' }} {{ request()->routeIs('quotations.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
        <i class="fas fa-file-invoice"></i> <span class="ml-2">Visites</span>
    </a>

    <a href="{{ route('queues.index') }}" 
       class="sidebar-link {{ request()->routeIs('queues.index') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
      <i class="fas fa-list"></i> <span class="ml-2">File d'attente</span>
    </a>

    <a href="{{ route('queues.show') }}" 
       class="sidebar-link {{ request()->routeIs('queues.show') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
      <i class="fas fa-list"></i> <span class="ml-2">Salle d'attente</span>
    </a>

    @if($user)
        <a href="{{ route('profiles.show', $user['id']) }}" 
        class="sidebar-link {{ request()->routeIs('profiles.show') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
            <i class="fa fa-user-circle"></i>
            <span class="ml-2">Mon Profile</span>
        </a>
    @endif

    {{-- Admin Links --}}
    @if(isset($authUser) && $authUser['role'] === 'admin')
        <a href="{{ route('admin.users.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }}
            flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
            <i class="fas fa-users-cog"></i> <span class="ml-2">Personnels</span>
        </a>
        <a href="{{ route('admin.logs') }}"
            class="sidebar-link {{ request()->routeIs('admin.logs') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }}
            flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
            <i class="fas fa-clipboard-list"></i> <span class="ml-2">Logs</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" 
            class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }}
            flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200 mb-1">
            <i class="fas fa-cogs"></i> <span class="ml-2">Paramètres système</span>
        </a>
    @endif
  </nav>
</div>

<style>
    /* Logo link */
    .logo-link {
        display: inline-block;
    }

    /* Logo image */
    .logo-img {
        height: 3rem;
        width: auto;
        object-fit: contain;
    }
</style>