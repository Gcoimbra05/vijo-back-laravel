@extends('layouts.app')
@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Logs - {{ $user->first_name }} {{ $user->last_name }}</h3>
                </div>
                <div class="card-body">
                    @if($auditLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Event</th>
                                        <th>Model</th>
                                        <th>Old Values</th>
                                        <th>New Values</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                        <th>Date/Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLogs as $index => $log)
                                        <tr>
                                            <td>{{ $auditLogs->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge badge-{{ $log->event == 'created' ? 'success' : ($log->event == 'updated' ? 'info' : 'danger') }}">
                                                    {{ ucfirst($log->event) }}
                                                </span>
                                            </td>
                                            <td>{{ class_basename($log->auditable_type) }}</td>
                                            <td>
                                                @if($log->old_values)
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            data-toggle="modal" 
                                                            data-target="#oldValuesModal{{ $log->id }}">
                                                        View
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->new_values)
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            data-toggle="modal" 
                                                            data-target="#newValuesModal{{ $log->id }}">
                                                        View
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->ip_address ?? '-' }}</td>
                                            <td>{{ substr($log->user_agent, 0, 50) ?? '-' }}...</td>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>

                                        @if($log->old_values)
                                            <div class="modal fade" id="oldValuesModal{{ $log->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Old Values</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <pre>{{ json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($log->new_values)
                                            <div class="modal fade" id="newValuesModal{{ $log->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">New Values</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <pre>{{ json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $auditLogs->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            No audit logs found for this user.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
