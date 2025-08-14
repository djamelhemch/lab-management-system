@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 shadow rounded-lg">


    {{-- Profile Update Form --}}
    @if(!empty($profile) && isset($profile['user_id']))
        <form method="POST" action="{{ route('profiles.update', $profile['user_id'] ?? '#') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6" enctype="multipart/form-data">

    @else
        <form method="POST" action="#" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @endif
        @csrf
        @method('PUT')
            {{-- Profile Photo --}}
            <div class="flex items-center gap-6 mb-8">
                <div class="relative w-28 h-28">
                    <label for="photo_input" class="cursor-pointer block w-full h-full rounded-full overflow-hidden border-4 border-gray-200 shadow hover:border-blue-500 transition duration-200">
                        <img id="profile_preview" src="{{ $profile['photo_url'] ?? 'https://via.placeholder.com/150' }}" alt="Profile Photo" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-30 opacity-0 hover:opacity-100 flex items-center justify-center text-white text-sm">Change</div>
                    </label>
                    <input id="photo_input" type="file" name="photo_file" class="hidden" accept="image/*">
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $profile['name'] ?? 'User Name' }}</h1>
                    <p class="text-gray-500">{{ $profile['email'] ?? '' }}</p>
                </div>
            </div>
        {{-- Theme --}}
        <div>
            <label class="block font-medium mb-1">Theme</label>
            <select name="theme" class="border p-2 w-full rounded">
                <option value="light" {{ isset($profile['theme']) && $profile['theme'] == 'light' ? 'selected' : '' }}>Light</option>
                <option value="dark" {{ isset($profile['theme']) && $profile['theme'] == 'dark' ? 'selected' : '' }}>Dark</option>
            </select>
        </div>

        {{-- Goals --}}
        <div>
            <label class="block font-medium mb-1">Goals (comma separated)</label>
            <textarea name="goals" class="border p-2 w-full rounded">{{ implode(', ', $profile['goals'] ?? []) }}</textarea>
        </div>

        {{-- Checklist --}}
        <div>
            <label class="block font-medium mb-1">Checklist (comma separated)</label>
            <textarea name="checklist" class="border p-2 w-full rounded">{{ implode(', ', $profile['checklist'] ?? []) }}</textarea>
        </div>

        <div class="col-span-1 md:col-span-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition">Update Profile</button>
        </div>
    </form>

    <hr class="my-8">

    {{-- Leave Requests --}}
    <h2 class="text-2xl font-bold mb-4">My Leave Requests</h2>

    <div class="overflow-x-auto mb-6">
        <table class="w-full border rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-3 text-left">Type</th>
                    <th class="border p-3 text-left">Reason</th>
                    <th class="border p-3 text-left">Status</th>
                    <th class="border p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveRequests as $leave)
                <tr>
                    <td class="border p-3">{{ $leave['type'] }}</td>
                    <td class="border p-3">{{ $leave['reason'] }}</td>
                    <td class="border p-3">
                        <span class="px-3 py-1 rounded-full text-white text-xs
                            {{ $leave['status'] == 'approved' ? 'bg-green-500' : ($leave['status'] == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                            {{ ucfirst($leave['status']) }}
                        </span>
                    </td>
                    <td class="border p-3 text-center">
                        <form method="POST" action="{{ route('leave-requests.destroy', $leave['id']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center p-4 text-gray-500">No leave requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create New Leave Request --}}
    <h3 class="text-lg font-bold mb-3">Create New Leave Request</h3>
    <form method="POST" action="{{ route('leave-requests.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        <div>
            <label class="block mb-1">Type</label>
            <select name="type" class="border p-2 w-full rounded">
                <option value="leave">Leave</option>
                <option value="certificate">Certificate</option>
                <option value="certificate_of_employment">Certificate of Employment</option>
            </select>
        </div>
        <div>
            <label class="block mb-1">Reason</label>
            <textarea name="reason" class="border p-2 w-full rounded"></textarea>
        </div>
        <div class="col-span-1 md:col-span-2">
            <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow transition">Submit</button>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('photo_input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile_preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
