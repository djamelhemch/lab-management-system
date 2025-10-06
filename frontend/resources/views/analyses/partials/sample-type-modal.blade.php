{{-- Sample Type Modal --}}
<div id="sampleTypeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 rounded-t-2xl">
                <h3 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Create New Sample Type
                </h3>
            </div>
            <form id="sampleTypeForm" class="p-6">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sample Type Name <span class="text-red-500">*</span></label>
                    <input type="text" id="sampleTypeName" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                           placeholder="e.g., Blood, Urine, Serum"
                           required>
                    <div id="sampleTypeError" class="text-red-600 text-sm mt-1.5 flex items-center gap-1">
                        <!-- Error will be inserted here -->
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeSampleTypeModal()" 
                            class="px-5 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 active:scale-95 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 active:scale-95 transition-all duration-200 shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Sample Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
