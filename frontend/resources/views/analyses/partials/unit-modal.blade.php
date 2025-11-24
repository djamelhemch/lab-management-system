{{-- Unit Modal --}}
<div id="unitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4 rounded-t-2xl">
                <h3 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Create New Unit
                </h3>
            </div>
            <form id="unitForm" class="p-6">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Name <span class="text-red-500">*</span></label>
                    <input type="text" id="unitName" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition duration-200" 
                           placeholder="e.g., mg/dL, mmol/L, g/L"
                           required>
                    <div id="unitError" class="text-red-600 text-sm mt-1.5 flex items-center gap-1">
                        <!-- Error will be inserted here -->
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeUnitModal()" 
                            class="px-5 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 active:scale-95 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 active:scale-95 transition-all duration-200 shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
