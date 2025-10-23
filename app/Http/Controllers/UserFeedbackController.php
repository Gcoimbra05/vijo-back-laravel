<?php
namespace App\Http\Controllers;

use App\Models\UserFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserFeedbackReceived;

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

        $nav_bar = 'userfeedback';
        $pageTitle = 'User Feedbacks';

        return view('admin.userfeedbacks.list', compact('userfeedbacks', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }
}
