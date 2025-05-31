@extends('layouts.app')

@section('content')
<div class="container">
    <h1><i class="fas fa-file-medical-alt"></i> Reports</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sample ID</th>
                <th>Findings</th>
                <th>Summary</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ $report->sample_id }}</td>
                    <td>{{ Str::limit($report->findings, 50) }}</td>
                    <td>{{ Str::limit($report->result_summary, 50) }}</td>
                    <td>{{ $report->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
