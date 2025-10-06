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
            // Log::error('Erro ao enviar email de feedback: ' . $e->getMessage());
        }

        return response()->json(['status' => true, 'message' => 'Feedback submitted successfully.', 'data' => $feedback]);
    }

    public function index()
    {
        $feedbacks = UserFeedback::with('user')->latest()->paginate(20);
        return response()->json($feedbacks);
    }
}
