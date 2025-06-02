@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Sample Details</h1>
    <div>
        <strong>ID:</strong> {{ $sample['id'] }}<br>
        <strong>Patient ID:</strong> {{ $sample['patient_id'] }}<br>
        <strong>Doctor ID:</strong> {{ $sample['doctor_id'] }}<br>
        <strong>Type:</strong> {{ $sample['sample_type'] }}<br>
        <strong>Volume:</strong> {{ $sample['volume_ml'] }} ml<br>
        <strong>Status:</strong> {{ ucfirst($sample['status']) }}<br>
        <strong>Barcode:</strong> {{ $sample['barcode'] }}<br>
        <!-- Add more fields as needed -->
    </div>
    <a href="{{ route('samples.index') }}" class="btn btn-secondary mt-3">Back to Samples</a>
</div>
@endsection