@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
                    <p class="mt-2 text-sm text-gray-600">Gérer les comptes utilisateurs, les rôles et les permissions</p>
                </div>
                
                <div class="flex items-center gap-4">
                    {{-- Stats Summary --}}
                    <div class="hidden sm:flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-600">{{ count($users) }} Utilisateurs</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <span class="text-gray-600">{{ count(array_filter($users, fn($u) => ($u['role'] ?? '') === 'admin')) }} Admins</span>
                        </div>
                    </div>
                    
                    {{-- Add User Button --}}
                    <a href="{{ route('admin.users.create') }}" 
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter un utilisateur
                    </a>
                </div>
            </div>
        </div>

        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 mb-8">
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Rechercher & Filtrer
                    </h2>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Search Input --}}
                    <div class="md:col-span-2">
                        <input type="text" id="userSearch" placeholder="Rechercher par nom ou email..." 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    
                    {{-- Role Filter --}}
                    <div>
                        <select id="roleFilter" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Tous les rôles</option>
                            <option value="admin">👑 Admin</option>
                            <option value="user">👤 Utilisateur</option>
                            <option value="manager">📊 Manager</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Répertoire des utilisateurs</h3>
                    <div class="flex items-center gap-3">
                        {{-- View Toggle --}}
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button id="tableView" class="px-3 py-1 rounded-md text-sm font-medium text-indigo-600 bg-white shadow-sm">
                                Tableau
                            </button>
                            <button id="cardView" class="px-3 py-1 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700">
                                Cartes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table View --}}
            <div id="tableContainer" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="usersTableBody">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors group user-row" 
                            data-name="{{ strtolower($user['full_name'] ?? 'N/A') }}" 
                            data-email="{{ strtolower($user['email'] ?? 'N/A') }}" 
                            data-role="{{ $user['role'] ?? '' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                            <span class="text-lg font-bold text-white">
                                                {{ substr($user['full_name'] ?? 'N', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $user['full_name'] ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">Membre depuis {{ date('M Y', strtotime($user['created_at'] ?? 'now')) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user['email'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">
                                    @if(isset($user['email_verified_at']) && $user['email_verified_at'])
                                        ✅ Vérifié
                                    @else
                                        ⏳ En attente
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block px-3 py-1 border border-gray-300 rounded-full text-xs font-semibold">
                                    {{ ucfirst($user['role'] ?? 'user') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user['is_connected'] ?? false)
                                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300 shadow-md">
                                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                        En ligne
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-600 border border-gray-300">
                                        Hors ligne
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('admin.users.show', $user['id']) }}" 
                                        class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user['id']) }}" 
                                        class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.users.destroy', $user['id']) }}" method="POST" class="inline delete-form" data-user-name="{{ $user['full_name'] ?? 'cet utilisateur' }}">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun utilisateur trouvé</h3>
                                    <p class="text-gray-500">Commencez par créer un nouvel utilisateur.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Card View (Hidden by default) --}}
            <div id="cardContainer" class="hidden p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($users as $user)
                <div class="user-card bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow"
                     data-name="{{ strtolower($user['full_name'] ?? 'N/A') }}" 
                     data-email="{{ strtolower($user['email'] ?? 'N/A') }}" 
                     data-role="{{ $user['role'] ?? '' }}">
                    <div class="text-center">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center mx-auto mb-4">
                            <span class="text-xl font-bold text-white">
                                {{ substr($user['full_name'] ?? 'N', 0, 1) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $user['full_name'] ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $user['email'] ?? 'N/A' }}</p>
                        <span class="inline-block px-3 py-1 border border-gray-300 rounded-full text-xs font-semibold">
                            {{ ucfirst($user['role'] ?? 'user') }}
                        </span>
                        
                        @if($user['is_connected'] ?? false)
                            <div class="flex items-center justify-center gap-2 mt-3 text-green-600 text-sm font-semibold">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                En ligne
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-2 mt-3 text-gray-500 text-sm font-semibold">
                                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                Hors ligne
                            </div>
                        @endif
                        
                        <div class="flex justify-center gap-2 mt-4">
                            <a href="{{ route('admin.users.show', $user['id']) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Voir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.users.edit', $user['id']) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user['id']) }}" method="POST" class="inline delete-form" data-user-name="{{ $user['full_name'] ?? 'cet utilisateur' }}">
                                @csrf 
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification Container --}}
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-4 pointer-events-none">
    @if(session('success'))
        <div class="toast-notification pointer-events-auto">
            {{-- Success Toast will be created by JavaScript --}}
        </div>
    @endif
</div>

{{-- Modal de Confirmation de Suppression --}}
<div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6 animate-pulse">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Supprimer le compte utilisateur</h3>
            <p class="text-base text-gray-600 mb-2">Êtes-vous sûr de vouloir supprimer</p>
            <p class="text-lg font-semibold text-gray-900 mb-4" id="deleteUserName">cet utilisateur</p>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-red-800 font-medium">⚠️ Cette action est irréversible</p>
                <p class="text-xs text-red-600 mt-1">Toutes les données de l'utilisateur seront définitivement supprimées</p>
            </div>
            <div class="flex gap-3 justify-center mb-4">
                <button type="button" onclick="proceedDelete()" 
                    class="px-6 py-3 text-white bg-red-600 rounded-xl hover:bg-red-700 font-semibold transition-all duration-200 transform hover:scale-105">
                    Oui, Supprimer
                </button>
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeDeleteModal()" 
                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 font-semibold transition-all duration-200 transform hover:scale-105">
                    Annuler
                </button>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const roleFilter = document.getElementById('roleFilter');
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const tableContainer = document.getElementById('tableContainer');
    const cardContainer = document.getElementById('cardContainer');
    
    // Show toast notification if session has success message
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    // Search and filter functionality
    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value.toLowerCase();
        const rows = document.querySelectorAll('.user-row');
        const cards = document.querySelectorAll('.user-card');
        
        [...rows, ...cards].forEach(element => {
            const name = element.dataset.name;
            const email = element.dataset.email;
            const role = element.dataset.role;
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !selectedRole || role === selectedRole;
            
            element.style.display = (matchesSearch && matchesRole) ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
    
    // View toggle functionality
    tableView.addEventListener('click', function() {
        tableContainer.classList.remove('hidden');
        cardContainer.classList.add('hidden');
        tableView.classList.add('text-indigo-600', 'bg-white', 'shadow-sm');
        tableView.classList.remove('text-gray-500');
        cardView.classList.add('text-gray-500');
        cardView.classList.remove('text-indigo-600', 'bg-white', 'shadow-sm');
    });
    
    cardView.addEventListener('click', function() {
        tableContainer.classList.add('hidden');
        cardContainer.classList.remove('hidden');
        cardView.classList.add('text-indigo-600', 'bg-white', 'shadow-sm');
        cardView.classList.remove('text-gray-500');
        tableView.classList.add('text-gray-500');
        tableView.classList.remove('text-indigo-600', 'bg-white', 'shadow-sm');
    });
    
    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
});

// Toast Notification Function
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 bg-white rounded-xl shadow-lg border-l-4 p-4 min-w-[320px] max-w-md transform translate-x-full transition-all duration-300 ${
        type === 'success' ? 'border-emerald-500' : 
        type === 'error' ? 'border-red-500' : 
        'border-blue-500'
    }`;
    
    toast.innerHTML = `
        <div class="flex-shrink-0">
            ${type === 'success' ? `
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            ` : type === 'error' ? `
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            ` : `
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            `}
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900">${type === 'success' ? 'Succès' : type === 'error' ? 'Erreur' : 'Information'}</p>
            <p class="text-sm text-gray-600 mt-0.5">${message}</p>
        </div>
        <button onclick="removeToast(this)" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Slide in animation
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 5000);
}

function removeToast(element) {
    const toast = element.tagName === 'BUTTON' ? element.closest('div[class*="flex items-center"]') : element;
    
    toast.classList.remove('translate-x-0');
    toast.classList.add('translate-x-full', 'opacity-0');
    
    setTimeout(() => {
        toast.remove();
    }, 300);
}

let formToSubmit = null;

function confirmDelete(button) {
    formToSubmit = button.closest('form');
    const userName = formToSubmit.dataset.userName || 'cet utilisateur';
    
    document.getElementById('deleteUserName').textContent = userName + '?';
    
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('deleteModalContent');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('deleteModalContent');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        formToSubmit = null;
    }, 300);
}

function proceedDelete() {
    if (formToSubmit) {
        formToSubmit.submit();
    }
}
</script>

@endsection
