@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">User Details</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
            ← Back to Users
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div><strong>Name:</strong> {{ $user['full_name'] }}</div>
        <div><strong>Email:</strong> {{ $user['email'] }}</div>
        <div><strong>Role:</strong> {{ ucfirst($user['role']) }}</div>
        <div><strong>Status:</strong> {{ ucfirst($user['status']) }}</div>

    </div>
</div>
@endsection
