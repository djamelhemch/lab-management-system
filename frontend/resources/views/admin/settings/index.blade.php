@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">System Settings</h1>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @foreach($settings as $setting)
        <div class="p-6 mb-6 border rounded-xl bg-white shadow">
            <h2 class="text-lg font-bold mb-4">{{ ucfirst($setting['name']) }}</h2>

            {{-- Options list --}}
            <div class="space-y-2">
                @foreach($setting['options'] as $option)
                    <div class="flex items-center justify-between bg-gray-100 p-2 rounded">
                        <span>
                            {{ $option['value'] }} 
                            @if($option['is_default'])
                                <span class="text-green-600 font-semibold">(default)</span>
                            @endif
                        </span>
                        <div class="flex space-x-2">
                            {{-- Set default --}}
                            @if(!$option['is_default'])
                                <form method="POST" action="{{ route('admin.settings.setDefault', [$setting['id'], $option['id']]) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded">
                                        Set Default
                                    </button>
                                </form>
                            @endif

                            {{-- Delete option --}}
                            <form method="POST" action="{{ route('admin.settings.deleteOption', $option['id']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Add new option --}}
            <form method="POST" action="{{ route('admin.settings.addOption', $setting['id']) }}" class="mt-4 flex items-center space-x-2">
                @csrf
                <input type="text" name="value" placeholder="New option value" class="border rounded-lg p-2 w-48" required>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_default" value="1">
                    <span>Default</span>
                </label>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Add
                </button>
            </form>
        </div>
    @endforeach
</div>
@endsection
