@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-10 flex items-center justify-center">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Créer un nouvel utilisateur</h1>
            <a href="{{ route('admin.users.index') }}" 
                class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors">
                ← Retour aux utilisateurs
            </a>
        </div>

        {{-- Alert Container for AJAX Messages --}}
        <div id="alert-container" class="mb-6"></div>

        <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="space-y-6">
                {{-- Username --}}
                <div>
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Nom d'utilisateur</label>
                    <input id="username" type="text" name="username" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-username"></span>
                </div>

                {{-- Full Name --}}
                <div>
                    <label for="full_name" class="block text-gray-700 text-sm font-semibold mb-2">Nom complet</label>
                    <input id="full_name" type="text" name="full_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-full_name"></span>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input id="email" type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-email"></span>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Rôle</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors">
                        <option value="" disabled selected>Sélectionner un rôle</option>
                        @foreach(['admin','biologist','technician','secretary','intern'] as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-600 text-sm" id="error-role"></span>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Mot de passe</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-password"></span>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-semibold mb-2">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-password_confirmation"></span>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                    Annuler
                </a>
                <button type="submit" id="submitBtn"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-400 font-semibold transition-colors">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Loading Modal with Spinner --}}
<div id="loadingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-sm mx-4 shadow-2xl">
        <div class="text-center">
            {{-- Animated Spinner --}}
            <div class="flex justify-center mb-6">
                <div class="relative w-20 h-20">
                    <!-- Background circle -->
                    <div class="absolute inset-0 border-4 border-blue-100 rounded-full"></div>
                    <!-- Spinning circle -->
                    <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
                </div>
            </div>
            
            {{-- Loading Text --}}
            <h3 class="text-xl font-bold text-gray-900 mb-2">Création en cours...</h3>
            <p class="text-sm text-gray-600">Veuillez patienter pendant que nous créons l'utilisateur</p>
            
            {{-- Animated Dots --}}
            <div class="flex justify-center gap-2 mt-4">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<div id="successModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-sm mx-4 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="successModalContent">
        <div class="text-center">
            {{-- Success Icon with Animation --}}
            <div class="mx-auto mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-dasharray: 50; stroke-dashoffset: 50; animation: drawCheck 0.5s ease-out forwards;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Success Text --}}
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Succès !</h3>
            <p class="text-sm text-gray-600 mb-4" id="successMessage">L'utilisateur a été créé avec succès</p>
            
            {{-- Redirect Info --}}
            <div class="flex items-center justify-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Redirection en cours...</span>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes drawCheck {
    to {
        stroke-dashoffset: 0;
    }
}

/* Ensure smooth spinner rotation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Bounce animation for dots with stagger */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

.animate-bounce {
    animation: bounce 1s ease-in-out infinite;
}
</style>

<script>
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');
    document.getElementById('alert-container').innerHTML = '';
    
    // Show loading modal
    showLoadingModal();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading modal
        hideLoadingModal();
        
        submitBtn.disabled = false;
        
        if (data.success) {
            // Show success modal
            showSuccessModal(data.message);
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = "{{ route('admin.users.index') }}";
            }, 2000);
        } else if (data.errors) {
            // Display validation errors
            Object.keys(data.errors).forEach(key => {
                const errorElement = document.getElementById(`error-${key}`);
                if (errorElement) {
                    errorElement.textContent = data.errors[key][0];
                }
            });
            
            // Show error alert
            document.getElementById('alert-container').innerHTML = `
                <div class="p-4 bg-red-50 border border-red-300 rounded-xl text-red-700 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Veuillez corriger les erreurs ci-dessous
                </div>
            `;
            
            // Scroll to top to show errors
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else if (data.error) {
            // Display API error (Username/Email exists)
            document.getElementById('alert-container').innerHTML = `
                <div class="p-4 bg-red-50 border border-red-300 rounded-xl text-red-700 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    ${data.error}
                </div>
            `;
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    })
    .catch(error => {
        hideLoadingModal();
        submitBtn.disabled = false;
        
        document.getElementById('alert-container').innerHTML = `
            <div class="p-4 bg-red-50 border border-red-300 rounded-xl text-red-700 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Une erreur s'est produite. Veuillez réessayer.
            </div>
        `;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

function showLoadingModal() {
    const modal = document.getElementById('loadingModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hideLoadingModal() {
    const modal = document.getElementById('loadingModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function showSuccessModal(message) {
    const modal = document.getElementById('successModal');
    const modalContent = document.getElementById('successModalContent');
    const messageElement = document.getElementById('successMessage');
    
    if (message) {
        messageElement.textContent = message;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}
</script>
@endsection
