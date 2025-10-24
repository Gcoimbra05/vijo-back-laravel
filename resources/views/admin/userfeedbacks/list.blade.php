@extends('layouts.app')

<style>
.input-container {
        display: flex;
        align-items: center;
        max-width: 400px;
        gap: 1rem;
        position: relative;
    }

    .fake-input {
        position: relative;
        font-size: 2.25rem;
    }
</style>

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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($userfeedbacks) && $userfeedbacks->count())
                    @foreach($userfeedbacks as $key => $userfeedback)
                        <tr>
                            <td>{{ optional($userfeedback->user)->id ?? '-' }}</td>
                            <td>{{ optional($userfeedback->user)->first_name ?? optional($userfeedback->user)->name ?? '-' }}</td>
                            <td>{{ optional($userfeedback->user)->email ?? '-' }}</td>
                            <td>{{ $userfeedback->type }}</td>
                            <td >{{ $userfeedback->subject }}</td>
                            <td class="text-truncate" style="max-width: 19rem;">{{ $userfeedback->message }}</td>
                            <td>{{ $userfeedback->created_at }}</td>
                            <td>    
                                <div>
                                    <span class="input-group-text bg-transparent border-0 p-0.3rem">
                                        <i class="bx bx-show me-1" title="View Feedback Details" data-bs-toggle="modal" data-bs-target="#myModal" style="font-size: 1.35rem; cursor: pointer;"></i> 
                                    </span>
                                </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="myModal" tabindex="2" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-md"> <!-- tamanho e centralização -->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div>
                                                    <h5 class="modal-title">{{ optional($userfeedback->user)->first_name ?? optional($userfeedback->user)->name ?? '-' }} feedback</h5>
                                                    <p class="modal-subtitle" style="font-size:0.75rem;">{{ optional($userfeedback->user)->email ?? '-' }}</p>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Back"></button>
                                                </div>

                                                <div class="modal-body">
                                                    
                                                        <div class="mb-3">
                                                            <label for="type" class="form-label"><strong>Type:</strong></label>
                                                            <div class="input-container" style="max-width: 200px;">
                                                                <div id="fakeInput" class="fake-input form-control form-control-lg @error('type') is-invalid @enderror">
                                                                    {{ $userfeedback->type }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="type" class="form-label"><strong>Subject:</strong></label>
                                                            <div class="input-container">
                                                                <div id="fakeInput" class="fake-input form-control form-control-lg @error('subject') is-invalid @enderror">
                                                                    {{ $userfeedback->subject }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="type" class="form-label"><strong>Message:</strong></label>
                                                            
                                                                <textarea class="form-control wide-input @error('userfeedbackmessage') is-invalid @enderror" id="userfeedbackmessage" name="userfeedbackmessage" style="cursor:default" readonly> {{ $userfeedback->message }} </textarea>
                                                            
                                                        </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <p class="modal-subtitle" style="font-size:0.75rem;">Created at: {{ $userfeedback->created_at }} /</p><br>
                                                    <p class="modal-subtitle" style="font-size:0.75rem;">Update at: {{ $userfeedback->update_at }}</p>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                            </td>
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