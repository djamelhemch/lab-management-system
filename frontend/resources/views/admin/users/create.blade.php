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

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="space-y-6">
                {{-- Username --}}
                <div>
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                </div>

                {{-- Full Name --}}
                <div>
                    <label for="full_name" class="block text-gray-700 text-sm font-semibold mb-2">Full Name</label>
                    <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                        @foreach(['admin','biologist','technician','secretary','intern'] as $role)
                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors" />
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-400 font-semibold transition-colors">
                    Create User
                </button>
            </div>

            {{-- Feedback Messages --}}
            <div class="mt-6 space-y-4">
                @if (session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-emerald-700 font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 bg-red-50 border border-red-300 rounded-xl text-red-700 font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-yellow-50 border border-yellow-300 rounded-xl text-yellow-700 font-semibold">
                        <strong>⚠️ There were some problems with your input:</strong>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>🔸 {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </form>
    </div>
</div>
@endsection
