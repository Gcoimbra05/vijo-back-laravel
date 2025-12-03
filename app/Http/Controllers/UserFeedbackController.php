<?php
namespace App\Http\Controllers;

use App\Models\UserFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserFeedbackReceived;
use Illuminate\Support\Facades\Log;

class UserFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'message' => 'required|string',
            'email' => 'nullable|email',
            'subject' => 'nullable|string',
        ]);

        $feedback = UserFeedback::create([
            'user_id' => Auth::id(),
            'type' => $request->input('type'),
            'message' => $request->input('message'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
        ]);

        try {
            $supportEmail = config('mail.support_email', 'admin@vijo.com');
            Mail::to($supportEmail)->send(new UserFeedbackReceived($feedback));
        } catch (\Exception $e) {
            Log::error('Error sending feedback email: ' . $e->getMessage());
        }

        return redirect()->route('userfeedback.index')
            ->with('success', 'User Feedback created successfully.');
    }

     public function index()
    {
        $userfeedbacks = UserFeedback::orderBy('id', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'UserFeedbacks retrieved successfully.',
                'data' => $UserFeedbacks,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'UserFeedbacks', 'url' => null],
        ];

        $nav_bar = 'userfeedbacks';
        $pageTitle = 'User Feedbacks';

        return view('admin.userfeedbacks.list', compact('userfeedbacks', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    public function show($id)
    {
        $feedback = UserFeedback::with('replies')->findOrFail($id);

        // If the feedback is unread, mark it as read when viewing via this controller method
        if ($feedback->status == UserFeedback::STATUS_UNREAD) {
            $feedback->status = UserFeedback::STATUS_READ;
            $feedback->save();
        }

        return view('admin.userfeedbacks.modal', compact('feedback'));
    }

    /**
     * Mark feedback as read.
     * If called via AJAX/JSON, return JSON with new status and label.
     */
    public function read($id)
    {
        $feedback = UserFeedback::with('replies')->findOrFail($id);

        if ($feedback->replies()->count() > 0) {
            $feedback->status = UserFeedback::STATUS_RESPONDED;
        } else {
            $feedback->status = UserFeedback::STATUS_READ;
        }

        // do not persist any "manual" flag here (handled in frontend temporarily)
        $feedback->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $feedback->status,
                'status_label' => $feedback->status_label,
            ]);
        }

        return redirect()->route('admin.userfeedbacks.index')
                        ->with('success', 'User feedback marked as read successfully.');
    }

    public function unread($id)
    {
        $feedback = UserFeedback::findOrFail($id);

        // Marca qualquer status como UNREAD (0)
        $feedback->status = UserFeedback::STATUS_UNREAD;
        $feedback->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $feedback->status,
                'status_label' => $feedback->status_label,
            ]);
        }

        return redirect()->route('admin.userfeedbacks.index')
                        ->with('success', 'User feedback marked as unread successfully.');
    }
}
