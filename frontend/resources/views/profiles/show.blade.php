@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            {{-- Profile Photo --}}
            <div class="relative w-36 h-36 rounded-full border-4 border-indigo-100 overflow-hidden group">
                <img id="profile_preview"
                     src="{{ $profile['photo_url'] ?? 'https://via.placeholder.com/150' }}"
                     alt="Profile Photo"
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                <label for="photo_input"
                       class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-full text-white font-medium cursor-pointer">
                    Change
                </label>
                <input id="photo_input" type="file" accept="image/*" name="photo_file" class="hidden">
            </div>

            {{-- Profile Info --}}
            <div class="flex flex-col text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $name }}</h1>
               <p class="text-gray-500 mt-2">{{ $profile['email'] ?? 'No email provided' }}</p>
            </div>
        </div>
    </div>

    {{-- Profile Form --}}
    <div class="mt-12 bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Profile Settings</h2>
        
        @if(!empty($profile) && isset($profile['user_id']))
            <form method="POST" action="{{ route('profiles.update', $profile['user_id']) }}" enctype="multipart/form-data" class="space-y-8" novalidate>
        @else
            <form method="POST" action="#" class="space-y-8" novalidate>
        @endif
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Theme --}}
                <div>
                    <label for="theme" class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                    <select id="theme" name="theme" class="w-full rounded-xl border-gray-300 shadow-sm px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="light" {{ (isset($profile['theme']) && $profile['theme'] == 'light') ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ (isset($profile['theme']) && $profile['theme'] == 'dark') ? 'selected' : '' }}>Dark</option>
                    </select>
                </div>

                {{-- Goals --}}
                <div>
                    <label for="goals" class="block text-sm font-medium text-gray-700 mb-2">Goals</label>
                    <textarea id="goals" name="goals" rows="4" placeholder="Enter goals separated by commas"
                              class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ implode(', ', $profile['goals'] ?? []) }}</textarea>
                </div>

                {{-- Checklist --}}
                <div class="md:col-span-2">
                    <label for="checklist" class="block text-sm font-medium text-gray-700 mb-2">Checklist</label>
                    <textarea id="checklist" name="checklist" rows="4" placeholder="Enter checklist items separated by commas"
                              class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ implode(', ', $profile['checklist'] ?? []) }}</textarea>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                        class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-lg transition transform hover:scale-105 focus:ring-4 focus:ring-indigo-300">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Leave Requests --}}
    <div class="mt-16">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">My Leave Requests</h2>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Reason</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $leave)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ ucfirst($leave['type']) }}</td>
                            <td class="px-6 py-4">{{ $leave['reason'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full text-white
                                    {{ $leave['status'] == 'approved' ? 'bg-green-500' : ($leave['status'] == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                    {{ ucfirst($leave['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('leave-requests.destroy', $leave['id']) }}" onsubmit="return confirm('Delete this request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                No leave requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- New Leave Request --}}
    <div class="mt-16">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Create New Leave Request</h3>
            <form method="POST" action="{{ route('leave-requests.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select id="type" name="type" class="w-full rounded-xl border-gray-300 shadow-sm px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="leave">Absence</option>
                        <option value="certificate">Document</option>
                        <option value="certificate_of_employment">Certificate of Employment</option>
                    </select>
                </div>
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea id="reason" name="reason" rows="4" placeholder="Describe your reason"
                              class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm resize-none focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <button type="submit"
                            class="px-8 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold shadow-md transition transform hover:scale-105 focus:ring-4 focus:ring-green-300">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
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
