@extends('layouts.app')
@section('title', 'Video Types List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>
        <a class="btn btn-label-primary btn-sm" type="button" href="{{ route('membership.add') }}" style="margin-bottom: 10px;">
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
                    <th>Monthly Cost</th>
                    <th>Annual Cost</th>
                    <th>Payment Mode</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Updated at</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($memberships) && $memberships->count())
                    @foreach($memberships as $key => $membership)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $membership->name }}</td>
                            <td>{{ $membership->monthly_cost }}</td>
                            <td>{{ $membership->annual_cost }}</td>
                            <td>
                                @switch($membership->payment_mode)
                                    @case(1) Debit @break
                                    @case(2) Credit @break
                                    @default Unknown
                                @endswitch
                            </td>
                            <td>{{ $membership->slug }}</td>
                            <td>
                                @switch($membership->status)
                                    @case(1) Activated @break
                                    @case(0) Deactivated @break
                                    @default {{ $membership->status }}
                                @endswitch
                            </td>
                            <td>{{ optional($membership->updated_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($membership->status == 1)
                                            <a class="dropdown-item" href="{{ route('membership.deactivate', $membership->id) }}">
                                                <i class="bx bx-x me-1"></i> Deactivate
                                            </a>
                                        @else
                                            <a class="dropdown-item" href="{{ route('membership.activate', $membership->id) }}">
                                                <i class="bx bx-check me-1"></i> Activate
                                            </a>
                                        @endif

                                        <a class="dropdown-item" href="{{ route('membership.edit', $membership->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <a class="dropdown-item" href="{{ route('membership.delete', $membership->id) }}" onclick="return confirm('Are you sure?');">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                @else
                    <tr>
                        <td colspan="9" class="text-center">No Memberships Plans found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
