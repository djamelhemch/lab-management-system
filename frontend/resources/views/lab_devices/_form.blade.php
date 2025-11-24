@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-xl rounded-2xl p-10 relative overflow-hidden">

    <div class="flex items-center justify-between mb-8 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            @if(isset($device))
                ✏️ Edit Device
            @else
                ➕ Add New Device
            @endif
        </h1>
        <a href="{{ route('lab-devices.index') }}" 
           class="text-blue-600 hover:text-blue-800 font-medium text-sm transition">
           ← Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg text-red-800 shadow-sm">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ isset($device) ? route('lab-devices.update', $device['id']) : route('lab-devices.store') }}">
        @csrf
        @if(isset($device))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach ([
                ['id'=>'name', 'label'=>'Name *', 'required'=>true],
                ['id'=>'manufacturer', 'label'=>'Manufacturer'],
                ['id'=>'model', 'label'=>'Model'],
                ['id'=>'ip_address', 'label'=>'IP Address'],
                ['id'=>'tcp_port', 'label'=>'TCP Port', 'type'=>'number'],
                ['id'=>'device_type', 'label'=>'Device Type']
            ] as $field)
            <div>
                <label for="{{ $field['id'] }}" class="block mb-2 text-sm font-semibold text-gray-700">
                    {{ $field['label'] }}
                </label>
                <input id="{{ $field['id'] }}" name="{{ $field['id'] }}" 
                    type="{{ $field['type'] ?? 'text' }}"
                    {{ isset($field['required']) ? 'required' : '' }}
                    value="{{ old($field['id'], $device[$field['id']] ?? '') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" />
            </div>
            @endforeach

            <div>
                <label for="connection_type" class="block mb-2 text-sm font-semibold text-gray-700">Connection Type</label>
                <select id="connection_type" name="connection_type"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                    @foreach(['tcp_client','tcp_server','serial','serial_via_converter','middleware'] as $option)
                        <option value="{{ $option }}" @if(old('connection_type', $device['connection_type'] ?? '') === $option) selected @endif>
                            {{ ucfirst(str_replace('_',' ', $option)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="protocol_type" class="block mb-2 text-sm font-semibold text-gray-700">Protocol Type</label>
                <select id="protocol_type" name="protocol_type"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                    @foreach(['HL7', 'ASTM', 'custom'] as $type)
                        <option value="{{ $type }}" @if(old('protocol_type', $device['protocol_type'] ?? '') === $type) selected @endif>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block mb-2 text-sm font-semibold text-gray-700">Status</label>
                <select id="status" name="status"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                    @foreach(['online','offline','error','testing'] as $opt)
                        <option value="{{ $opt }}" @if(old('status', $device['status'] ?? '') === $opt) selected @endif>
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block mb-2 text-sm font-semibold text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">{{ old('description', $device['description'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-10 flex justify-end sticky bottom-0 bg-white pt-4">
            <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold px-8 py-3 rounded-lg shadow transition">
                💾 Save Device
            </button>
        </div>
    </form>
</div>
@endsection
