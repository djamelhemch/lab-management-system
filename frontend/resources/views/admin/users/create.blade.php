@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-10 flex items-center justify-center">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
            <a href="{{ route('admin.users.index') }}" 
                class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors">
                ← Back to Users
            </a>
        </div>

        {{-- Alert Container for AJAX Messages --}}
        <div id="alert-container" class="mb-6"></div>

        <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="space-y-6">
                {{-- Username --}}
                <div>
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                    <input id="username" type="text" name="username" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-username"></span>
                </div>

                {{-- Full Name --}}
                <div>
                    <label for="full_name" class="block text-gray-700 text-sm font-semibold mb-2">Full Name</label>
                    <input id="full_name" type="text" name="full_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-full_name"></span>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input id="email" type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-email"></span>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors">
                        <option value="" disabled selected>Select Role</option>
                        @foreach(['admin','biologist','technician','secretary','intern'] as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-600 text-sm" id="error-role"></span>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-password"></span>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                    <span class="text-red-600 text-sm" id="error-password_confirmation"></span>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-400 font-semibold transition-colors">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');
    document.getElementById('alert-container').innerHTML = '';
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        if (data.success) {
            // Show success message
            document.getElementById('alert-container').innerHTML = `
                <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-emerald-700 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    ${data.message}
                </div>
            `;
            
            // Redirect after 1.5 seconds
            setTimeout(() => {
                window.location.href = "{{ route('admin.users.index') }}";
            }, 1500);
        } else if (data.errors) {
            // Display validation errors
            Object.keys(data.errors).forEach(key => {
                const errorElement = document.getElementById(`error-${key}`);
                if (errorElement) {
                    errorElement.textContent = data.errors[key][0];
                }
            });
        } else if (data.error) {
            // Display API error (Username/Email exists)
            document.getElementById('alert-container').innerHTML = `
                <div class="p-4 bg-red-50 border border-red-300 rounded-xl text-red-700 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    ${data.error}
                </div>
            `;
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        document.getElementById('alert-container').innerHTML = `
            <div class="p-4 bg-red-50 border border-red-300 rounded-xl text-red-700 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                An error occurred. Please try again.
            </div>
        `;
    });
});
</script>
@endsection
