@extends('layouts.app')
@section('title', 'Journal History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Journal History - {{ $user->first_name }} {{ $user->last_name }}</h3>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Journals</h5>
                                    <h2>{{ $stats['total_journals'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Completed</h5>
                                    <h2>{{ $stats['completed_journals'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pending</h5>
                                    <h2>{{ $stats['pending_journals'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Videos</h5>
                                    <h2>{{ $stats['total_videos'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($journalHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Catalog</th>
                                        <th>Status</th>
                                        <th>Video</th>
                                        <th>Duration</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($journalHistory as $index => $journal)
                                        <tr>
                                            <td>{{ $journalHistory->firstItem() + $index }}</td>
                                            <td>
                                                @if($journal->catalog)
                                                    <span title="{{ $journal->catalog->description }}">
                                                        {{ $journal->catalog->emoji ?? '' }} {{ $journal->catalog->title }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No catalog</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $journal->status == 'completed' ? 'success' : ($journal->status == 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($journal->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($journal->video && $journal->video->url)
                                                    <a href="{{ $journal->video->url }}" target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-play"></i> View
                                                    </a>
                                                @else
                                                    <span class="text-muted">No video</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($journal->video && $journal->video->duration)
                                                    {{ gmdate("i:s", $journal->video->duration) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $journal->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $journal->updated_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" 
                                                        data-toggle="modal" 
                                                        data-target="#detailsModal{{ $journal->id }}">
                                                    Details
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Details Modal -->
                                        <div class="modal fade" id="detailsModal{{ $journal->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Journal Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>ID:</strong> {{ $journal->id }}<br>
                                                                <strong>Status:</strong> {{ $journal->status }}<br>
                                                                <strong>Created:</strong> {{ $journal->created_at }}<br>
                                                                <strong>Updated:</strong> {{ $journal->updated_at }}<br>
                                                            </div>
                                                            <div class="col-md-6">
                                                                @if($journal->catalog)
                                                                    <strong>Catalog:</strong> {{ $journal->catalog->title }}<br>
                                                                    <strong>Description:</strong> {{ $journal->catalog->description }}<br>
                                                                @endif
                                                                @if($journal->video)
                                                                    <strong>Video Duration:</strong> {{ $journal->video->duration }}s<br>
                                                                    <strong>Video URL:</strong> <a href="{{ $journal->video->url }}" target="_blank">View</a><br>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            
                        </div>
                    @else
                        <div class="alert alert-info">
                            No journal history found for this user.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
