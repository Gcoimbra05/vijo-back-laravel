<h2>New Feedback Received</h2>
<p><strong>Type:</strong> {{ $feedback->type }}</p>
<p><strong>Subject:</strong> {{ $feedback->subject ?? '-' }}</p>
<p><strong>Message:</strong><br>{{ $feedback->message }}</p>
<p><strong>User Email:</strong> {{ $feedback->email ?? '-' }}</p>
<p><strong>User ID:</strong> {{ $feedback->user_id ?? '-' }}</p>
