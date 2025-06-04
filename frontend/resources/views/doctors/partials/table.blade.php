@if (empty($doctors))
    <div class="text-center text-gray-600">
        <p>No doctors found.</p>
        <a href="{{ route('doctors.create') }}" class="mt-4 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Create Your First Doctor
        </a>
    </div>
@else
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
           <thead class="bg-gray-100 text-sm font-medium text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Specialty</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Prescriber</th>
                    <th class="px-4 py-3 text-left">Patients</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @foreach ($doctors as $doctor)
                    <tr class="hover:bg-gray-50 border-t">
                        <td class="px-4 py-3">
                            <a href="{{ route('doctors.show', $doctor['id']) }}" class="text-red-600 hover:text-red-800 font-medium">
                                {{ $doctor['full_name'] }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $doctor['specialty'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $doctor['phone'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $doctor['email'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 rounded-full text-xs {{ $doctor['is_prescriber'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $doctor['is_prescriber'] ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $doctor['patient_count'] ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('doctors.show', $doctor['id']) }}" 
                                   class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs font-semibold transition">
                                    View
                                </a>
                                <a href="{{ route('doctors.edit', $doctor['id']) }}" 
                                   class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-semibold transition">
                                    Edit
                                </a>
                                <button 
                                    type="button"
                                    onclick="showDeleteModal({{ $doctor['id'] }}, '{{ addslashes($doctor['full_name']) }}')"
                                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs font-semibold transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif