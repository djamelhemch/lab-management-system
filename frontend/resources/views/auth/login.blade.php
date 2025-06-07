@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center">Login to Abdelatif Lab</h2>

    @if($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium" for="username">Username</label>
            <input id="username" name="username" type="text" required class="w-full px-3 py-2 border rounded-lg">
        </div>

        <div class="mb-6">
            <label class="block mb-1 text-sm font-medium" for="password">Password</label>
            <input id="password" name="password" type="password" required class="w-full px-3 py-2 border rounded-lg">
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
            Login
        </button>
    </form>
@endsection