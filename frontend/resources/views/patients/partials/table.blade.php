@if (empty($patients))
    <div class="text-center text-gray-600">
        <p>No patients found.</p>
        <a href="{{ route('patients.create') }}" class="mt-4 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Create Your First Patient
        </a>
    </div>
@else
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-sm font-medium text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Dossier N°</th>
                    <th class="px-4 py-3 text-left">Nom Prénom</th>
                    <th class="px-4 py-3 text-left">Sexe</th>
                    <th class="px-4 py-3 text-left">Age</th>
                    <th class="px-4 py-3 text-left">Médecin Traitant</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @foreach ($patients as $p)
                    <tr class="hover:bg-gray-50 border-t">
                        <td class="px-4 py-3">{{ $p['file_number'] }}</td>
                        <td class="px-4 py-3">{{ $p['first_name'] }} {{ $p['last_name'] }}</td>
                        <td class="px-4 py-3">{{ $p['gender'] }}</td>
                        <td class="px-4 py-3">{{ $p['age'] }} ans</td>
                        <td class="px-4 py-3">
                            {{ $p['doctor_name'] ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 flex space-x-2">
                            <a href="{{ route('patients.show', $p['id']) }}" class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('patients.edit', $p['id']) }}" class="text-yellow-600 hover:underline">Edit</a>
                            <form action="{{ route('patients.destroy', $p['id']) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif