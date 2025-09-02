@extends('layouts.app')
@section('title', 'Users List')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2"> Users </h5>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->mobile }}</td>
                        <td>{{ $user->status }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>

                                <div class="dropdown-menu">
                                    <?php if ($user->status == 1) { ?>
                                        <a class="dropdown-item" href="<?php echo route('users.deactivate', $user->id); ?>"><i class="bx bx-x me-1"></i> Deactivate</a>
                                    <?php } else if ($user->status == 0) { ?>
                                        <a class="dropdown-item" href="<?php echo route('users.activate', $user->id); ?>"><i class="bx bx-check me-1"></i> Activate</a>
                                    <?php } ?>

                                    <a class="dropdown-item" href="<?php echo route('users.journalHistory', $user->id); ?>"><i class="bx bx-history me-1"></i> Journal History</a>
                                    <a class="dropdown-item" href="<?php echo route('users.auditLogs', $user->id); ?>"><i class="bx bxl-blogger me-1"></i> Audit Logs</a>

                                    <!-- lets user form here.. -->
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item"><i class="bx bx-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
