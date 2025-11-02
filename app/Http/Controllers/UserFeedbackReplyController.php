<?php

namespace App\Http\Controllers;

use App\Models\UserFeedbackReply;
use Illuminate\Http\Request;
use App\Models\UserFeedback;

class UserFeedbackReplyController extends Controller
{
    /**
     * LISTEN
     */
    public function index()
    {
        $userfeedbackreplies = UserFeedbackReply::with(['feedback', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        $breadcrumbs = [
            ['label' => 'User Feedback Replies', 'url' => null],
        ];

        $nav_bar = 'userfeedbackreply';
        $pageTitle = 'User Feedback Replies';

        return redirect()->route('userfeedbacks.index');
    }

    /**
     * CREATE
     */
    public function create()
    {
        $pageTitle = "Add Feedback Reply";
        $nav_bar = "userfeedbackreply";
        $breadcrumbs = [
            ['label' => 'Feedback Replies', 'url' => route('userfeedbackreply.index')],
            ['label' => 'Add Feedback Reply', 'url' => null],
        ];

        $userfeedbackreply = null;

        return view('admin.userfeedbacks.modalfeedback', compact(
            'pageTitle',
            'nav_bar',
            'breadcrumbs',
            'userfeedbackreply'
        ));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_feedback_id' => 'required|integer|exists:user_feedbacks,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'type' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Cria a resposta
        $reply = UserFeedbackReply::create([
            'user_feedback_id' => $validated['user_feedback_id'],
            'user_id' => $validated['user_id'],
            'type' => $validated['type'] ?? 'General',
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'created_at' => now(), // define explicitamente
        ]);

        // Atualiza status do feedback original
        $feedback = UserFeedback::find($validated['user_feedback_id']);
        if ($feedback) {
            $feedback->status = UserFeedback::STATUS_RESPONDED; // 2
            $feedback->save();
        }

        // 🚀 Força resposta JSON se a requisição vier via AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'reply' => [
                    'id' => $reply->id,
                    'subject' => e($reply->subject),
                    'message' => e($reply->message),
                    'created_at' => $reply->created_at->format('d/m/Y H:i'),
                ],
                'status_label' => $feedback->status_label ?? 'Unknown',
            ], 200);
        }

        // fallback para acessos normais
        return redirect()
            ->route('userfeedbacks.index')
            ->with('success', 'Feedback reply created successfully.');
    }



    /**
     * SHOW 
     */
    public function show($id)
    {
        $userfeedbackreply = UserFeedbackReply::findOrFail($id);

        $pageTitle = "View Feedback Reply";
        $nav_bar = "userfeedbackreply";
        $breadcrumbs = [
            ['label' => 'Feedback Replies', 'url' => route('userfeedbackreply.index')],
            ['label' => 'View Feedback Reply', 'url' => null],
        ];

        return view('admin.userfeedbacks.modalfeedback', compact(
            'userfeedbackreply',
            'pageTitle',
            'nav_bar',
            'breadcrumbs'
        ));
    }
}
