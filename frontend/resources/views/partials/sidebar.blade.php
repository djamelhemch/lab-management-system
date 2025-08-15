@php
    $user = session('user'); // FastAPI authenticated user
@endphp
<div id="sidebar" class="bg-white w-64 flex flex-col space-y-6 py-7 px-2 fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-50 shadow-2xl md:shadow-none">
    <div class="flex items-center justify-between px-4 mb-2">
        <a href="#" class="logo-link">
            <img src="/images/logo_lab.PNG" alt="Abdelatif Lab" class="logo-img">
        </a>
        <button id="closeSidebar" class="md:hidden text-gray-600 focus:outline-none">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

   <nav class="flex flex-col gap-1 mt-6">
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} {{ request()->routeIs('dashboard') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-tachometer-alt"></i> <span class="ml-2">Tableau de bord</span>
    </a>
    <a href="{{ route('patients.index') }}" class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }} {{ request()->routeIs('patients.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-user-injured"></i> <span class="ml-2">Patients</span>
    </a>
    <a href="{{ route('doctors.index') }}" class="sidebar-link {{ request()->routeIs('doctors.*') ? 'active' : '' }} {{ request()->routeIs('doctors.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-user-md"></i> <span class="ml-2">Médecins</span>
    </a>
    <a href="{{ route('samples.index') }}" class="sidebar-link {{ request()->routeIs('samples.*') ? 'active' : '' }} {{ request()->routeIs('samples.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-vial"></i> <span class="ml-2">Échantillons</span>
    </a>
    <!--lien vers resultats {{ route('reports.index') }} -->
    <a href="#"  class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }} {{ request()->routeIs('reports.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-file-medical"></i> <span class="ml-2">Résultat</span>
    </a>
    <a href="{{ route('analyses.index') }}" class="sidebar-link {{ request()->routeIs('analyses.*') ? 'active' : '' }} {{ request()->routeIs('analyses.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">  
        <i class="fas fa-flask"></i> <span class="ml-2">Analyses</span>  
    </a>
    
    
    {{-- NEW LINKS --}}
    <a href="{{ route('quotations.index') }}" class="sidebar-link {{ request()->routeIs('quotations.*') ? 'active' : '' }} {{ request()->routeIs('quotations.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-file-invoice"></i> <span class="ml-2">Devis</span>
    </a>
     <a href="{{ route('queues.index') }}" 
       class="sidebar-link {{ request()->routeIs('queues.index') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
      <i class="fas fa-list"></i></i> <span class="ml-2">File d'attente</span>
    </a>
    <a href="{{ route('queues.show') }}" 
       class="sidebar-link {{ request()->routeIs('queues.show') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
      <i class="fas fa-list"></i></i> <span class="ml-2">Salle d'attente</span>
    </a>
    @if($user)
        <a href="{{ route('profiles.show', $user['id']) }}" 
        class="sidebar-link {{ request()->routeIs('profiles.show') ? 'active bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }} flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
            <i class="fa fa-user-circle"></i>
            <span class="ml-2">Mon Profile</span>
        </a>
    @endif


    {{-- Admin Links --}}

    @if(isset($authUser) && $authUser['role'] === 'admin')
        <a href="{{ route('admin.users.index') }}"
        class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'bg-red-100 text-red-700 font-bold shadow' : 'text-gray-700 hover:bg-red-50' }}
        flex items-center gap-3 py-2.5 px-4 rounded-lg transition-colors duration-200">
        <i class="fas fa-users-cog"></i> <span class="ml-2">Personnels</span>
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
    height: 3rem;      /* Adjust the height as needed */
    width: auto;       /* Maintain aspect ratio */
    object-fit: contain;
}
</style>