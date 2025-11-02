<style>
.circle {
  width: 5rem;
  aspect-ratio: 1 / 1;
  border-radius: 50%;
  background-color: #E4E7EB;
  border: 1px solid #B0B7BD;

  display: flex;
  justify-content: center;
  align-items: center;

  font-weight: bold;
  font-size: 1.2rem;
  color: #333;
  margin-left: 2px
}


.email-reply .circle {
    background-color: #1E0E84;
    color: #fff;
    margin-left: 0rem;
    margin-right: 2px;
}

.reply-bubble {
    background-color: #D9DEE3;
    padding: 1.1rem;
    border-radius: 1rem 0rem 1rem 1rem;
    border: 1px solid #B0B7BD;
    text-align: right;
    max-width: 75%;
    margin-top: 1.4rem;
    box-shadow: 0 1px 0 rgba(0,0,0,0.05);
    transform-origin: bottom right;
    margin-top:1.3rem;
}

/* animation: slight rise + fade-in */
@keyframes bubbleRise {
    from {
        transform: translateY(8px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.reply-bubble.new {
    animation: bubbleRise 260ms ease-out;
}

/* smaller subject box inside bubble */
.reply-bubble .fake-input {
    background-color: #f8f9fa;
    cursor: default;
}

/* ensure replies align to right (sender side) */
.row.email-reply {
    justify-content: flex-end;
}

/* ensure received message (user) stays left */
.row.email-received {
    justify-content: flex-start;
}
</style>

<!-- Modal -->
<div class="modal fade" id="feedbackModal{{ $userfeedback->id }}" data-feedback-id="{{ $userfeedback->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h5 class="modal-title">
                    {{ optional($userfeedback->user)->first_name ?? optional($userfeedback->user)->name ?? '-' }} feedback
                    <span id="feedbackStatusLabel_{{ $userfeedback->id }}" class="badge bg-secondary ms-2">
                        {{ $userfeedback->status_label ?? '' }}
                    </span>
                </h5>
                <p class="modal-subtitle" style="font-size:0.75rem;">{{ $userfeedback->email ?? '-' }}</p>
            </div>
            <div style="text-align: right;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Back"></button>
                <p class="modal-subtitle" style="font-size:0.75rem;">Created at: {{ $userfeedback->created_at }}</p>
            </div>
        </div>

        <div class="modal-body">
            <!-- Original Message -->
            <div class="row email-received mb-4">
                <div class="col-1 d-flex align-items-start justify-content-center">
                    <div class="circle">
                        {{ strtoupper(substr(optional($userfeedback->user)->first_name ?? optional($userfeedback->user)->name ?? '-', 0, 1)) }}
                    </div>
                </div>
                <div class="col-10" style="background-color: #b3cce4ff; padding:1.1rem; border-radius:0rem 1rem 1rem 1rem; border:1px solid #B0B7BD; margin-top:1.3rem">
                    <div class="mb-1">
                        <label><strong>Subject:</strong></label>
                        <div class="input-container">
                            <div class="fake-input form-control form-control-lg" style="background-color:#f8f9fa;">
                                {{ $userfeedback->subject }}
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label><strong>Message:</strong></label>
                        <textarea class="form-control" readonly style="cursor:default;">{{ $userfeedback->message }}</textarea>
                    </div>
                    <p class="modal-subtitle mb-0" style="font-size:0.75rem; text-align:right;">{{ $userfeedback->created_at }}</p>
                </div>
            </div>

            <!-- Replies -->
            <div id="repliesContainer_{{ $userfeedback->id }}">
                @if($userfeedback->replies->count())
                    @foreach($userfeedback->replies as $reply)
                        <div class="row email-reply mb-4">
                            <div class="col-10 offset-1 reply-bubble">
                                <div class="mb-1 offset-5">
                                    <label><strong>Subject:</strong></label>
                                    <div class="input-container">
                                        <div class="fake-input form-control form-control-lg" style="background-color:#f8f9fa;">{{ $reply->subject }}</div>
                                    </div>
                                </div>
                                <div class="mb-3 mt-2">
                                    <label><strong>Message:</strong></label>
                                    <textarea class="form-control" readonly>{{ $reply->message }}</textarea>
                                </div>
                                <p class="modal-subtitle mb-0" style="font-size:0.75rem; text-align:right;">{{ optional($reply->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-1 d-flex align-items-start justify-content-center">
                                <div class="circle">V</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="mb-3 no-replies text-center" style="font-size:1.2rem">
                        <label><strong>No replies yet.</strong></label>
                    </div>
                @endif
            </div>

            <!-- Reply Form -->
            <hr>
            <h5>Reply</h5>
            <form class="replyForm" data-feedback-id="{{ $userfeedback->id }}" method="POST" action="{{ $formAction }}">
                @csrf
                <input type="hidden" name="user_feedback_id" value="{{ $userfeedback->id }}">
                <input type="hidden" name="user_id" value="{{ $userfeedback->user_id }}">
                <input type="hidden" name="type" value="{{ $userfeedback->type }}">

                <div class="mb-3">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Message</label>
                    <textarea name="message" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Email</button>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
        </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avoid registering handlers multiple times when this partial is included many times
    if (window.replyFormHandlerInitialized) {
        return;
    }
    window.replyFormHandlerInitialized = true;

    window.manualUnreadSet = window.manualUnreadSet || new Set();

    function updateStatus(feedbackId, label, manual=false) {
        const display = manual ? '● ' + label : label;
        const badge = document.getElementById('feedbackStatusLabel_' + feedbackId);
        const row = document.getElementById('feedbackStatus_' + feedbackId);
        if (badge) badge.textContent = display;
        if (row) row.textContent = display;
    }

    // Event delegation for all .replyForm forms
    document.addEventListener('submit', async function(e) {
        const form = e.target;
        if (!form.classList.contains('replyForm')) return;

        e.preventDefault();
        const feedbackId = form.dataset.feedbackId;
        if (!feedbackId) return;

        // try to find a real submit button; if none, use a virtual object to hold sending flag
        let submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        let isVirtualBtn = false;
        if (!submitBtn) {
            // create a minimal virtual object to track sending state
            submitBtn = { disabled: false, dataset: {} };
            isVirtualBtn = true;
        }

        // Prevent double send
        if (submitBtn.dataset && submitBtn.dataset.sending === '1') return;

        if (!isVirtualBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.sending = '1';
        } else {
            submitBtn.dataset.sending = '1';
        }

        // CSRF token fallback: prefer form token, else meta tag
        let csrfTokenValue = null;
        const csrfInput = form.querySelector('input[name="_token"]');
        if (csrfInput) {
            csrfTokenValue = csrfInput.value;
        } else {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) csrfTokenValue = meta.getAttribute('content');
        }

        if (!csrfTokenValue) {
            console.warn('CSRF token not found in form or meta tag. Request may be rejected by server.');
        }

        const formData = new FormData(form);

        try {
            const resp = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfTokenValue || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!resp.ok) {
                const text = await resp.text();
                console.error('Network response was not ok:', text);
                throw new Error('Network response was not ok');
            }

            let data;
            try {
                data = await resp.json();
            } catch (parseErr) {
                console.warn('Failed to parse JSON response, falling back to form values', parseErr);
                data = {
                    success: true,
                    reply: {
                        subject: form.querySelector('[name="subject"]')?.value || '',
                        message: form.querySelector('[name="message"]')?.value || '',
                        created_at: new Date().toLocaleString(),
                    },
                    status_label: 'Responded'
                };
            }

            if (data && data.success && data.reply) {
                const container = document.getElementById('repliesContainer_' + feedbackId);
                if (!container) throw new Error('Replies container not found');

                const noRepliesEl = container.querySelector('.no-replies');
                if (noRepliesEl) noRepliesEl.remove();

                const row = document.createElement('div');
                row.className = 'row email-reply mb-4';

                const bubble = document.createElement('div');
                bubble.className = 'col-10 offset-1 reply-bubble new';
                bubble.innerHTML = `
                    <div class="mb-1 offset-5">
                        <label><strong>Subject:</strong></label>
                        <div class="input-container">
                            <div class="fake-input form-control form-control-lg" style="background-color:#f8f9fa;">
                                ${data.reply.subject}
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label><strong>Message:</strong></label>
                        <textarea class="form-control" readonly>${data.reply.message}</textarea>
                    </div>
                    <p class="modal-subtitle mb-0" style="font-size:0.75rem; text-align:right;">
                        ${data.reply.created_at}
                    </p>
                `;
                row.appendChild(bubble);

                const avatar = document.createElement('div');
                avatar.className = 'col-1 d-flex align-items-start justify-content-center';
                avatar.innerHTML = '<div class="circle">V</div>';
                row.appendChild(avatar);

                container.appendChild(row);
                row.scrollIntoView({ behavior: 'smooth', block: 'end' });

                // Update status displays
                updateStatus(feedbackId, data.status_label || 'Responded', false);
                window.manualUnreadSet.delete(String(feedbackId));

                form.reset();
                setTimeout(() => bubble.classList.remove('new'), 600);
            } else {
                console.error('Invalid response from server:', data);
                alert('Error sending feedback reply. Please try again.');
            }

        } catch(err) {
            console.error('Error submitting reply:', err);
            alert('Error sending the form.');
        } finally {
            // re-enable real submit button if present; clear sending flag either way
            if (!isVirtualBtn) {
                submitBtn.disabled = false;
                delete submitBtn.dataset.sending;
            } else {
                delete submitBtn.dataset.sending;
            }
        }
    });

    // Mark feedback as read when any modal opens (registered once)
    document.querySelectorAll('.modal').forEach(modalEl => {
        modalEl.addEventListener('shown.bs.modal', async function() {
            const feedbackId = this.dataset.feedbackId;
            if (!feedbackId) return;

            try {
                const resp = await fetch(`{{ url('admin/userfeedbacks/read') }}/${feedbackId}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With':'XMLHttpRequest','Accept':'application/json' }
                });
                if (!resp.ok) throw new Error('Network error');

                const data = await resp.json();
                updateStatus(feedbackId, data.status_label || 'Read', false);
            } catch(err) {
                console.error('Error marking read:', err);
            }
        });
    });
});
</script>
