@extends('layouts.guest')

@section('title', 'Connexion - Abdelatif Lab')

@section('content')
<div class="bg-white rounded-2xl shadow-2xl p-8 animate-fade-in">
    
    {{-- Logo Section --}}
    <div class="flex flex-col items-center mb-8">
        <div class="w-24 h-24 mb-4 rounded-full bg-gradient-to-br from-red-500 to-rose-600 p-1 shadow-lg">
            <div class="w-full h-full rounded-full bg-white flex items-center justify-center overflow-hidden">
                <img src="{{ $logoUrl }}" 
                     alt="Abdelatif Lab Logo" 
                     class="w-20 h-20 object-contain">
            </div>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Abdelatif Lab</h2>
        <p class="text-gray-500 text-sm mt-1">Système de Gestion de Laboratoire</p>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg animate-fade-in">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Erreur de connexion</p>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
        @csrf

        {{-- Username Field --}}
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user text-gray-400 mr-2"></i>Nom d'utilisateur
            </label>
            <input id="username" 
                   name="username" 
                   type="text" 
                   required 
                   value="{{ old('username') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                   placeholder="Entrez votre nom d'utilisateur"
                   autocomplete="username">
        </div>

        {{-- Password Field --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock text-gray-400 mr-2"></i>Mot de passe
            </label>
            <div class="relative">
                <input id="password" 
                       name="password" 
                       type="password" 
                       required 
                       class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                       placeholder="••••••••"
                       autocomplete="current-password">
                <button type="button" 
                        onclick="togglePassword()" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <i id="toggleIcon" class="fas fa-eye"></i>
                </button>
            </div>
        </div>
<!-- 
        {{-- Remember Me --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="remember" 
                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>
        </div> -->

        {{-- Submit Button --}}
        <button type="submit" 
                class="w-full bg-gradient-to-r from-[#ff6b6b] to-red-500 hover:from-red-600 hover:to-red-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
        </button>
    </form>

    {{-- Footer --}}
    <div class="mt-8 text-center text-xs text-gray-500">
        <p>© {{ date('Y') }} Abdelatif Lab. Tous droits réservés.</p>
    </div>
</div>

{{-- Password Toggle Script --}}
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Auto-hide error after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const errorBox = document.querySelector('.bg-red-50');
    if (errorBox) {
        setTimeout(() => {
            errorBox.style.opacity = '0';
            errorBox.style.transition = 'opacity 0.5s';
            setTimeout(() => errorBox.remove(), 500);
        }, 5000);
    }
});
</script>
@endsection
