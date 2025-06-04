{{-- resources/views/quotations/partials/table.blade.php --}}
<table class="min-w-full bg-white rounded shadow">
    <thead>
        <tr>
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2">Patient</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($quotations as $quotation)
            <tr>
                <td class="px-4 py-2">{{ $quotation['id'] }}</td>
                <td class="px-4 py-2">
                    {{ $quotation['patient']['full_name'] ?? 'N/A' }}
                </td>
                <td class="px-4 py-2 capitalize">{{ $quotation['status'] }}</td>
                <td class="px-4 py-2 font-semibold">${{ number_format($quotation['total'], 2) }}</td>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($quotation['created_at'])->format('Y-m-d H:i') }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('quotations.show', $quotation['id']) }}" class="text-blue-600 hover:underline">Show</a>
                    <a href="{{ route('quotations.edit', $quotation['id']) }}" class="text-yellow-600 hover:underline ml-2">Edit</a>
                    <form action="{{ route('quotations.destroy', $quotation['id']) }}" method="POST" class="inline" onsubmit="return confirm('Delete this quotation?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline ml-2">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No quotations found.</td>
            </tr>
        @endforelse
    </tbody>
</table>