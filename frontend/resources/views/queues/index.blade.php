@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-6">
    {{-- Notifications --}}
    <x-alerts />

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Queue Management</h1>
            <p class="mt-1 text-gray-600">Monitor and control patient flow</p>
        </div>

        {{-- Add to Queue Form --}}
        <form method="POST" action="{{ route('queues.store') }}" class="w-full lg:w-auto bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add to Queue</h2>
            <div class="space-y-4">
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                    <select name="patient_id" id="patient_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="">Select Patient</option>
                        @foreach($patients as $id => $name)
                            <option value="{{ $id }}" {{ old('patient_id')==$id?'selected':'' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quotation_id" class="block text-sm font-medium text-gray-700">Quotation (optional)</label>
                    <input type="number" name="quotation_id" id="quotation_id" value="{{ old('quotation_id') }}" placeholder="#ID"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" />
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Queue Type</label>
                    <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="">Select Type</option>
                        <option value="reception" {{ old('type')=='reception'?'selected':'' }}>Reception</option>
                        <option value="blood_draw" {{ old('type')=='blood_draw'?'selected':'' }}>Blood Draw</option>
                    </select>
                </div>
                <button type="submit" class="w-full flex items-center justify-center py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold transition">
                    <i class="fas fa-plus mr-2"></i> Add to Queue
                </button>
            </div>
        </form>
    </div>

    {{-- Queues Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach(['Reception' => $receptionQueue, 'Blood Draw' => $bloodDrawQueue] as $title => $queue)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 flex justify-between items-center border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $title }} Queue</h3>
                    <p class="text-sm text-gray-600">Waiting: {{ count($queue) }}</p>
                </div>
                @if($title === 'Reception')
                <form method="POST" action="{{ route('queues.moveNext') }}">
                    @csrf
                    <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">Move Next &#8594;</button>
                </form>
                @endif
            </div>

            <ul class="divide-y divide-gray-200 max-h-[28rem] overflow-y-auto">
                @forelse($queue as $item)
                    <li class="px-6 py-4 flex justify-between items-start hover:bg-gray-50 transition">
                        <div class="flex items-start space-x-4">
                            <div>
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-800 font-semibold">
                                    {{ $item['position'] }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $patients[$item['patient_id']] ?? 'Patient #'.$item['patient_id'] }}</h4>
                                @if($item['quotation_id'])
                                    <p class="text-xs text-gray-500">Quotation: <span class="font-medium">#{{ $item['quotation_id'] }}</span></p>
                                @endif
                               
                            </div>
                        </div>
                        <form method="POST" action="{{ route('queues.destroy', $item['id']) }}" onsubmit="return confirm('Remove this patient?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50 transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-clock fa-2x mb-2 text-gray-400"></i>
                        <p class="text-sm">No patients in {{ strtolower($title) }} queue.</p>
                    </li>
                @endforelse
            </ul>
        </div>
        @endforeach
    </div>
</div>
@endsection
