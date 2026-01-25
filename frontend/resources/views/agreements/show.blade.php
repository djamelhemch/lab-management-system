@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('agreements.index') }}" 
                   class="mr-4 text-gray-600 hover:text-gray-900 transition-colors duration-150"
                   title="Retour">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Détails de la Convention</h1>
                    <p class="text-gray-600 text-sm mt-1">Consultez les informations complètes de cette convention</p>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Informations de la convention</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $agreement['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        <span class="w-2 h-2 rounded-full mr-2 {{ $agreement['status'] === 'active' ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                        {{ ucfirst($agreement['status']) }}
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Discount Type -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Type de remise
                        </div>
                        <div class="text-lg font-medium text-gray-900">
                            {{ ucfirst($agreement['discount_type']) }}
                        </div>
                    </div>

                    <!-- Discount Value -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Valeur de remise
                        </div>
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-blue-600">{{ $agreement['discount_value'] }}</span>
                            <span class="ml-2 text-lg text-gray-500">
                                {{ $agreement['discount_type'] === 'percentage' ? '%' : '€' }}
                            </span>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Statut
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold {{ $agreement['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    @if($agreement['status'] === 'active')
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ ucfirst($agreement['status']) }}
                            </span>
                        </div>
                    </div>

                    <!-- Created Date -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Créée le
                        </div>
                        <div class="text-lg font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($agreement['created_at'])->format('d/m/Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            à {{ \Carbon\Carbon::parse($agreement['created_at'])->format('H:i') }}
                        </div>
                    </div>

                </div>

                <!-- Description Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Description
                    </div>
                    @if($agreement['description'])
                        <div class="bg-gray-50 rounded-lg p-4 text-gray-700 leading-relaxed">
                            {{ $agreement['description'] }}
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 text-gray-400 italic text-center">
                            Aucune description fournie
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('agreements.edit', $agreement['id']) }}" 
               class="inline-flex items-center justify-center bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-3 rounded-lg hover:from-amber-600 hover:to-amber-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            
            <form action="{{ route('agreements.destroy', $agreement['id']) }}" 
                  method="POST" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette convention ? Cette action est irréversible.')"
                  class="flex-shrink-0">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </form>
            
            <a href="{{ route('agreements.index') }}" 
               class="inline-flex items-center justify-center bg-white text-gray-700 px-6 py-3 rounded-lg border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Retour à la liste
            </a>
        </div>

        <!-- Additional Info Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">Informations</h3>
                    <p class="text-xs text-blue-800">
                        Cette convention {{ $agreement['status'] === 'active' ? 'est actuellement active et peut être appliquée aux transactions' : 'est inactive et ne peut pas être utilisée actuellement' }}.
                        @if($agreement['discount_type'] === 'percentage')
                            La remise de {{ $agreement['discount_value'] }}% sera calculée sur le montant total.
                        @else
                            Un montant fixe de {{ $agreement['discount_value'] }}€ sera déduit du total.
                        @endif
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
