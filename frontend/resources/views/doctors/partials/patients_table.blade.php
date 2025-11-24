@if (empty($patients))
    <div class="text-center text-gray-600 py-8">
        <p>No patients assigned to this doctor.</p>
        <a href="#" class="mt-2 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Assign First Patient
        </a>
    </div>
@else
    <!-- Patients Dropdown/List -->
    <div class="space-y-2 max-h-64 overflow-y-auto" id="patients-list">
        @foreach ($patients as $patient)
            <div class="patient-item p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                data-patient-name="{{ strtolower($patient['full_name'] ?? '') }}"
                data-file-number="{{ strtolower($patient['file_number'] ?? '') }}">
                <div class="flex justify-between items-center">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $patient['full_name'] ?? 'N/A' }}</p>
                        <div class="flex flex-wrap gap-2 text-sm text-gray-600 mt-1">
                            <span>File: {{ $patient['file_number'] ?? 'N/A' }}</span>
                            <span>•</span>
                            <span>{{ $patient['phone'] ?? 'No phone' }}</span>
                            @if($patient['age'])
                                <span>•</span>
                                <span>Age: {{ $patient['age'] }}</span>
                            @endif
                            @if($patient['blood_type'])
                                <span>•</span>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">{{ $patient['blood_type'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('patients.show', $patient['id']) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                            View Patient
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif