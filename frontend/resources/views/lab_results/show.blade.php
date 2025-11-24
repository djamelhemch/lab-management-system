@extends('layouts.app')

@section('title', 'Lab Result Details')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <a href="{{ route('lab-results.index') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
        ← Back to all results
    </a>

    <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-200">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    🧾 Laboratory Result Report
                </h1>
                <p class="text-gray-500 text-sm">Result ID: #{{ $result['id'] }}</p>
            </div>
            <a href="{{ route('lab-results.download', $result['id']) }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm shadow">
                ⬇ Download PDF
            </a>
        </div>

        {{-- 🧍 Patient Information --}}
        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Patient Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <p><span class="text-gray-500">Name:</span>
                    <span class="font-medium">{{ $result['patient_first_name'] ?? '' }} {{ $result['patient_last_name'] ?? '' }}</span>
                </p>
                <p><span class="text-gray-500">File Number:</span>
                    <span class="font-mono text-gray-700">{{ $result['file_number'] ?? '—' }}</span>
                </p>
            </div>
        </div>

        {{-- 🔬 Analysis Information --}}
        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Analysis Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <p><span class="text-gray-500">Code:</span>
                    <span class="font-medium">{{ $result['analysis_code'] ?? 'N/A' }}</span>
                </p>
                <p><span class="text-gray-500">Name:</span>
                    <span class="font-medium">{{ $result['analysis_name'] ?? 'N/A' }}</span>
                </p>
                <p><span class="text-gray-500">Quotation ID:</span>
                    <span class="font-medium">{{ $result['quotation_id'] ?? '—' }}</span>
                </p>
                <p><span class="text-gray-500">Status:</span>
                    @php
                        $statusColor = match($result['status']) {
                            'final' => 'bg-green-100 text-green-700',
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp
                    <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $statusColor }}">
                        {{ ucfirst($result['status'] ?? 'Unknown') }}
                    </span>
                </p>
            </div>
        </div>

        {{-- ⚗️ Result Information --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Result</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <p><span class="text-gray-500">Result Value:</span>
                    <span class="font-semibold text-gray-800">{{ $result['result_value'] ?? '—' }}</span>
                </p>
                <p><span class="text-gray-500">Normal Range:</span>
                    @if(!is_null($result['normal_min']) && !is_null($result['normal_max']))
                        <span class="font-semibold text-gray-800">{{ $result['normal_min'] }} – {{ $result['normal_max'] }}</span>
                    @else
                        <span class="text-gray-400 italic">Not available</span>
                    @endif
                </p>
                <p><span class="text-gray-500">Interpretation:</span>
                    @php
                        $badgeColor = match(strtolower($result['interpretation'] ?? '')) {
                            'low' => 'bg-blue-100 text-blue-700',
                            'normal' => 'bg-green-100 text-green-700',
                            'high' => 'bg-yellow-100 text-yellow-700',
                            'critical' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-500'
                        };
                    @endphp
                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $badgeColor }}">
                        {{ ucfirst($result['interpretation'] ?? 'n/a') }}
                    </span>
                </p>
                <p><span class="text-gray-500">Device Used:</span>
                    <span class="font-medium">{{ $result['device_name'] ?? '—' }}</span>
                </p>
                <p><span class="text-gray-500">Date Performed:</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($result['created_at'])->format('d/m/Y H:i') }}</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
