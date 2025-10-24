@extends('layouts.app')

@section('title', 'User Feedbacks')

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
                    <th>User ID</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Created at</th>
                    <th>Update at</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($userfeedbacks) && $userfeedbacks->count())
                    @foreach($userfeedbacks as $key => $userfeedback)
                        <tr>
                            <td>{{ $userfeedback->user_id }}</td>
                            <td>{{ $userfeedback->type }}</td>
                            <td>{{ $userfeedback->email }}</td>
                            <td>{{ $userfeedback->subject }}</td>
                            <td>{{ $userfeedback->message }}</td>
                            <td>{{ $userfeedback->created_at }}</td>
                            <td>{{ $userfeedback->update_at }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">No user feedbacks found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

@endsection