@extends('layouts.app')

@section('content')
{{-- Toast Notification Container --}}
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
<div class="p-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Analyses</h1>
            <p class="text-sm text-gray-600">
                {{ request('show') === 'all'
                    ? 'Showing all analyses (including inactive)'
                    : 'Showing active analyses only' }}
            </p>
        </div>

        <a href="{{ route('analyses.create') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full shadow-sm bg-blue-600 text-white hover:bg-blue-700">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Analysis
        </a>
    </div>

    <x-search-filter
        search-placeholder="Search by name, code, or category..."
        :search-value="request('q')"
        :categories="$categories"
        :category-value="request('category_analyse_id')"
        category-name="category_analyse_id"
        category-label="Category"
        form-id="analysis-search-form"
        :table-route="route('analyses.table')"
        container-id="analyses-table-container"
    />

    <div id="analyses-table-container" class="mt-4">
        @include('analyses.partials.table', [
            'analyses' => $analyses,
            'showAll'  => $showAll ?? (request('show') === 'all')
        ])
    </div>
</div>
@endsection
<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    toast.id = toastId;
    
    const colors = {
        success: 'bg-green-500 border-green-600',
        error: 'bg-red-500 border-red-600',
        warning: 'bg-orange-500 border-orange-600',
        info: 'bg-blue-500 border-blue-600'
    };
    
    const icons = {
        success: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>`,
        error: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`,
        warning: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>`,
        info: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
               </svg>`
    };
    
    toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg border-l-4 flex items-center gap-3 min-w-[320px] max-w-md toast-enter`;
    toast.innerHTML = `
        <div class="flex-shrink-0">${icons[type]}</div>
        <div class="flex-1">
            <p class="font-medium text-sm">${message}</p>
        </div>
        <button onclick="closeToast('${toastId}')" class="flex-shrink-0 hover:bg-white/20 rounded p-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => closeToast(toastId), 4000);
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (!toast) return;
    
    toast.classList.remove('toast-enter');
    toast.classList.add('toast-exit');
    
    setTimeout(() => toast.remove(), 300);
}

// Show flash messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @elseif(session('error'))
        showToast("{{ session('error') }}", 'error');
    @elseif(session('warning'))
        showToast("{{ session('warning') }}", 'warning');
    @elseif(session('info'))
        showToast("{{ session('info') }}", 'info');
    @endif
});
</script>

<style>
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.toast-enter { animation: slideInRight 0.3s ease-out forwards; }
.toast-exit { animation: slideOutRight 0.3s ease-in forwards; }
</style>