@extends('layouts.app')

@section('title', 'Jobs List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>

    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Queue</th>
                    <th>Payload</th>
                    <th>Attempts</th>
                    <th>Reserved at</th>
                    <th>Available at</th>
                    <th>Created at</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($jobs) && $jobs->count())
                    @foreach($jobs as $key => $job)
                        <tr>
                            <td>{{ $job-> id }}</td>
                            <td>{{ $job->queue }}</td>
                            <td>{{ $job->payload }}</td>
                            <td>{{ $job->attempts }}</td>
                            <td>{{ $job->reserved_at ? date('Y-m-d H:i:s', $job->reserved_at) : '-' }}</td>
                            <td>{{ date('Y-m-d H:i:s', $job->available_at) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $job->created_at) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="12" class="text-center">No jobs found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

@endsection
