@extends('layouts.app')
@section('title', 'Video Types List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>
        <a class="btn btn-label-primary btn-sm" type="button" href="{{ route('tag.add') }}" style="margin-bottom: 10px;">
            <span class="tf-icons bx bx-plus me-1"></span> Add New
        </a>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Category ID</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created by</th>
                    <th>Updated at</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($tags) && $tags->count())
                    @foreach($tags as $key => $tag)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>{{ $tag->category->name ?? '-' }}</td>
                            <td>{{ $tag->type }}</td>
                             <td>
                                @switch($tag->status)
                                    @case(1) Activated @break
                                    @case(0) Deactivated @break
                                    @default {{ $tag->status }}
                                @endswitch
                            </td>
                            <td>{{ $tag->creator->first_name ?? $tag->creator->name ?? 'N/A' }}</td>
                            <td>{{ optional($tag->updated_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($tag->status == 1)
                                            <a class="dropdown-item" href="{{ route('tag.deactivate', $tag->id) }}">
                                                <i class="bx bx-x me-1"></i> Deactivate
                                            </a>
                                        @else
                                            <a class="dropdown-item" href="{{ route('tag.activate', $tag->id) }}">
                                                <i class="bx bx-check me-1"></i> Activate
                                            </a>
                                        @endif

                                        <a class="dropdown-item" href="{{ route('tag.edit', $tag->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <a class="dropdown-item" href="{{ route('tag.delete', $tag->id) }}" onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">No tags found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
