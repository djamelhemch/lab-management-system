@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage user accounts, roles, and permissions</p>
                </div>
                
                <div class="flex items-center gap-4">
                    {{-- Stats Summary --}}
                    <div class="hidden sm:flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-600">{{ count($users) }} Total Users</span>
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
                        Add User
                    </a>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-emerald-800 font-medium">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 mb-8">
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search & Filter
                    </h2>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Search Input --}}
                    <div class="md:col-span-2">
                        <input type="text" id="userSearch" placeholder="Search users by name or email..." 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    
                    {{-- Role Filter --}}
                    <div>
                        <select id="roleFilter" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">All Roles</option>
                            <option value="admin">👑 Admin</option>
                            <option value="user">👤 User</option>
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
                    <h3 class="text-lg font-semibold text-gray-800">Users Directory</h3>
                    <div class="flex items-center gap-3">
                        {{-- View Toggle --}}
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button id="tableView" class="px-3 py-1 rounded-md text-sm font-medium text-indigo-600 bg-white shadow-sm">
                                Table
                            </button>
                            <button id="cardView" class="px-3 py-1 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700">
                                Cards
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
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
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
                                        <div class="text-xs text-gray-500">Member since {{ date('M Y', strtotime($user['created_at'] ?? 'now')) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user['email'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">
                                    @if(isset($user['email_verified_at']) && $user['email_verified_at'])
                                        ✅ Verified
                                    @else
                                        ⏳ Pending
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block px-3 py-1 border rounded-full text-xs font-semibold 'border-gray-300' }}">
                                {{ ucfirst($user['role'] ?? 'user') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user['is_connected'] ?? false)
                                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300 shadow-md">
                                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                        Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-600 border border-gray-300">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('admin.users.show', $user['id']) }}" 
                                        class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="View User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user['id']) }}" 
                                        class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.users.destroy', $user['id']) }}" method="POST" class="inline delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete User">
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
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No users found</h3>
                                    <p class="text-gray-500">Get started by creating a new user.</p>
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
                        <span class="inline-block px-3 py-1 border rounded-full text-xs font-semibold 'border-gray-300' }}">
                            {{ ucfirst($user['role'] ?? 'user') }}
                        </span>
                        
                        @if($user['is_connected'] ?? false)
                            <div class="flex items-center justify-center gap-2 mb-4 text-green-600 font-semibold">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                Online
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-2 mb-4 text-gray-500 font-semibold">
                                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                Offline
                            </div>
                        @endif
                        
                        <div class="flex justify-center gap-2 mt-4">
                            <a href="{{ route('admin.users.show', $user['id']) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg" title="View User">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.users.edit', $user['id']) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit User">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-sm mx-4 shadow-xl">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete User</h3>
            <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button onclick="proceedDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const roleFilter = document.getElementById('roleFilter');
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const tableContainer = document.getElementById('tableContainer');
    const cardContainer = document.getElementById('cardContainer');
    
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
});

let formToSubmit = null;

function confirmDelete(button) {
    formToSubmit = button.closest('form');
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    formToSubmit = null;
}

function proceedDelete() {
    if (formToSubmit) {
        formToSubmit.submit();
    }
    closeDeleteModal();
}
</script>
@endsection
