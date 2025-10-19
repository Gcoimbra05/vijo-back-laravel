@extends('layouts.app')

@section('title', 'Jobs List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>
        
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>uuid</th>
                    <th>connection</th>
                    <th>queue</th>
                    <th>payload</th>
                    <th>exception</th>
                    <th>failed at</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($failed_jobs) && $failed_jobs->count())
                    @foreach($failed_jobs as $key => $failed_job)
                        <tr>
                            <td>{{ $failed_job-> id }}</td>
                            <td>{{ $failed_job->uuid }}</td>
                            <td>{{ $failed_job->connection }}</td>
                            <td>{{ $failed_job->queue }}</td>
                            <td>{{ $failed_job->payload }}</td>
                            <td>{{ $failed_job->exception }}</td>
                            <td>{{ date('Y-m-d H:i:s', $failed_job->failed_at) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">No failed Jobs found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

@endsection