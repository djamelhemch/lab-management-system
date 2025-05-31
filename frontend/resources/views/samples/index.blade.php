@extends('layouts.app')

@section('content')
<div class="container">
    <h1><i class="fas fa-vial"></i> Samples</h1>
    <a href="{{ route('samples.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus-circle"></i> Add Sample</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Type</th>
                <th>Status</th>
                <th>Collected</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($samples as $sample)
                <tr>
                    <td>{{ $sample->id }}</td>
                    <td>{{ $sample->patient->full_name ?? '-' }}</td>
                    <td>{{ $sample->doctor->full_name ?? '-' }}</td>
                    <td>{{ $sample->sample_type }}</td>
                    <td><span class="badge bg-info">{{ $sample->status }}</span></td>
                    <td>{{ $sample->collection_date }}</td>
                    <td>
                        <a href="{{ route('samples.show', $sample->id) }}" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('samples.destroy', $sample->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
