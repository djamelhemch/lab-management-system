@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
            ← Back to Users
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Role</label>
                <select name="role" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(['admin','biologist','technician','secretary','intern'] as $role)
                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">New Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection