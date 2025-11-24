@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-6">Add New Lab Device</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="list-disc ml-5">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lab-devices.store') }}">
        @csrf
        @include('lab_devices._form')
    </form>
</div>
@endsection
