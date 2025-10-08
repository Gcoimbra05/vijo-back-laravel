<?php

namespace App\Http\Controllers;

use App\Models\QuickGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuickGoalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goals = QuickGoal::where('user_id', $user->id)->get();
        return response()->json($goals);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount_of_videos' => 'required|integer|min:1',
            'period_type' => 'required|in:daily,weekly,monthly',
            'end_date' => 'nullable',
        ]);
        $today = now()->toDateString();
        $days = 1;
        if ($request->period_type === 'weekly') {
            $days = 7;
        } elseif ($request->period_type === 'monthly') {
            $days = 30;
        }
        $period_start = $today;
        $period_end = $request->end_date ? Carbon::createFromFormat('Y-m-d', $request->end_date)->toDateString() : now()->addDays($days)->toDateString();

        $goal = QuickGoal::where('user_id', Auth::id())
            ->first();
        if (!$goal) {
            $goal = QuickGoal::create([
                'user_id' => Auth::id(),
                'amount_of_videos' => $request->amount_of_videos,
                'period_type' => $request->period_type,
                'period_start' => $period_start,
                'period_end' => $period_end,
                'recorded' => 0,
                'status' => 'active',
            ]);
        } else {
            $goal->update([
                'amount_of_videos' => $request->amount_of_videos,
                'period_type' => $request->period_type,
                'period_start' => $period_start,
                'period_end' => $period_end,
                'recorded' => 0,
                'status' => 'active',
            ]);
        }

        $goal->period_end = Carbon::parse($goal->period_end)->format('M d, Y');

        return response()->json($goal, 201);
    }

    public function show($id)
    {
        $goal = QuickGoal::findOrFail($id);
        return response()->json($goal);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $goal = QuickGoal::where('user_id', $userId)
            ->where('status', 'active')
            ->whereDate('period_end', '>=', now()->toDateString())
            ->latest('period_end')
            ->first();

        if (!$goal) {
            return response()->json(['message' => 'No active goal found for the user'], 404);
        }

        $request->validate([
            'amount_of_videos' => 'sometimes|integer|min:1',
            'period_type' => 'sometimes|in:daily,weekly,monthly',
            'status' => 'sometimes|in:active,completed,inactive',
            'end_date' => 'sometimes|date_format:Y-m-d',
        ]);
        $goal->update($request->only([
            'amount_of_videos', 'period_type', 'status', 'end_date'
        ]));

        // Format period_end before returning
        $goal->period_end = Carbon::parse($goal->period_end)->format('M d, Y');

        return response()->json($goal);
    }

    public function destroy($id)
    {
        $goal = QuickGoal::findOrFail($id);
        $goal->delete();
        return response()->json(['message' => 'Goal deleted successfully']);
    }

    public function incrementRecorded($userId)
    {
        Log::info('incrementRecorded called for user_id: ' . $userId);

        $activeGoal = QuickGoal::where('user_id', $userId)
            ->whereIn('status', ['active', 'completed'])
            ->whereDate('period_end', '>=', now()->toDateString())
            ->first();

        if ($activeGoal) {
            $activeGoal->recorded += 1;

            if ($activeGoal->recorded >= $activeGoal->amount_of_videos) {
                $activeGoal->status = 'completed';
            }

            $activeGoal->save();
        }
    }

    public static function getQuickGoalInfo($userId = null)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $activeGoal = QuickGoal::where('user_id', $userId)
            ->whereIn('status', ['completed', 'active'])
            ->whereDate('period_end', '>=', now()->toDateString())
            ->latest('period_end')
            ->first();

        if ($activeGoal && $activeGoal->recorded > 0 && !Carbon::parse($activeGoal->updated_at)->isToday()) {
            $activeGoal->update(['recorded' => 0]);
        }

        if ($activeGoal) {
            return [
                'id' => $activeGoal->id,
                'amount_of_videos' => $activeGoal->amount_of_videos,
                'recorded' => $activeGoal->recorded,
                'period_type' => $activeGoal->period_type,
                'period_start' => $activeGoal->period_start,
                'period_end' => Carbon::parse($activeGoal->period_end)->format('M d, Y'),
                'status' => $activeGoal->status,
            ];
        } else {
            return [];
        }
    }
}
