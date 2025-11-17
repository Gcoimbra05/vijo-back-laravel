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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($userfeedbacks) && $userfeedbacks->count())
                    @foreach($userfeedbacks as $key => $userfeedback)
                        <tr>
                            <td>{{ optional($userfeedback->user)->id ?? '-' }}</td>
                            <td>{{ optional($userfeedback->user)->first_name ?? optional($userfeedback->user)->name ?? '-' }}</td>
                            <td>{{ $userfeedback->email ?? '-' }}</td>
                            <td>{{ $userfeedback->type }}</td>
                            <td class="text-truncate" style="max-width: 10rem;">{{ $userfeedback->subject }}</td>
                            <td class="text-truncate" style="max-width: 12rem;">{{ $userfeedback->message }}</td>
                            <td style="font-size:0.85rem;">{{ $userfeedback->created_at }}</td>
                            <td id="feedbackStatus_{{ $userfeedback->id }}"
                                data-feedback-id="{{ $userfeedback->id }}"
                                data-status="{{ $userfeedback->status }}">
                                {{ $userfeedback->status_label }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <div class="dropdown-menu">

                                        <a class="dropdown-item" title="View Feedback Details" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $userfeedback->id }}" style="cursor:pointer">
                                            <i class="bx bx-show me-1"></i> View
                                        </a>

                                         @if(in_array($userfeedback->status, [1,2]))
                                            <a class="dropdown-item mark-unread" href="#" data-feedback-id="{{ $userfeedback->id }}">
                                                <i class="bx bx-x me-1"></i> Mark as unread
                                            </a>
                                        @else
                                            <a class="dropdown-item mark-read" href="#" data-feedback-id="{{ $userfeedback->id }}">
                                                <i class="bx bx-check me-1"></i> Mark as read
                                            </a>
                                        @endif


                                    </div>

                                </div>
                            </td>
                        </tr>

                        @include('admin.userfeedbacks.modalfeedback', [
                            'userfeedback' => $userfeedback,
                            'userfeedbackreply' => $userfeedback->replies->last() ?? null,
                            'formAction' => route('userfeedbackreply.store')
                        ])
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    async function markAsUnread(feedbackId) {
        const td = document.querySelector(`#feedbackStatus_${feedbackId}`);
        if (!td) return;

        try {
            const resp = await fetch('{{ url("admin/userfeedbacks/unread") }}/' + feedbackId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!resp.ok) throw new Error('Network error');

            const data = await resp.json();

            // Atualiza status no frontend: sempre Unread ●
            td.dataset.status = 0;
            td.textContent = 'Unread ●';
        } catch (err) {
            console.error(err);
            alert('Failed to mark feedback as unread.');
        }
    }

    async function markAsRead(feedbackId) {
        const td = document.querySelector(`#feedbackStatus_${feedbackId}`);
        if (!td) return;

        try {
            const resp = await fetch('{{ url("admin/userfeedbacks/read") }}/' + feedbackId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!resp.ok) throw new Error('Network error');

            const data = await resp.json();

            // Atualiza status no frontend conforme backend
            td.dataset.status = data.status;
            td.textContent = data.status_label;
        } catch (err) {
            console.error(err);
            alert('Failed to mark feedback as read.');
        }
    }

    // Dropdowns
    document.querySelectorAll('.mark-unread').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            markAsUnread(a.dataset.feedbackId);
        });
    });

    document.querySelectorAll('.mark-read').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            markAsRead(a.dataset.feedbackId);
        });
    });

    // Click direto na célula para marcar unread (Read ou Responded)
    document.querySelectorAll('td[id^="feedbackStatus_"]').forEach(td => {
        td.style.cursor = 'pointer';
        td.addEventListener('click', () => {
            const status = parseInt(td.dataset.status, 10);
            if ([1,2].includes(status)) {
                markAsUnread(td.dataset.feedbackId);
            }
        });
    });

});
</script>


@endsection
