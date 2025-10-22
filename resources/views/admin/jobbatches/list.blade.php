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
                    <th>Name</th>
                    <th>Total jobs</th>
                    <th>Pending jobs</th>
                    <th>Failed job ids</th>
                    <th>Failed jobs</th>
                    <th>Created at</th>
                    <th>cancelled at</th>
                    <th>Finished at</th>
                    <th>Options</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($job_batches) && $job_batches->count())
                    @foreach($job_batches as $key => $job_batch)
                        <tr>
                            <td>{{ $job_batch-> id }}</td>
                            <td>{{ $job_batch->name }}</td>
                            <td>{{ $job_batch->total_jobs }}</td>
                            <td>{{ $job_batch->pending_jobs }}</td>
                            <td>{{ $job_batch->failed_job_ids }}</td>
                            <td>{{ $job_batch->failed_jobs }}</td>
                            <td>{{ date('Y-m-d H:i:s', $job_batch->created_at) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $job_batch->cancelled_at) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $job_batch->finished_at) }}</td>
                            <td>{{ $job_batch->options }}</td>
                           
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" class="text-center">No Job Batches found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

@endsection