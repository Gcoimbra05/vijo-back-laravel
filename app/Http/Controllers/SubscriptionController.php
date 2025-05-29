<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::all();
        $responseData = [
            'status'  => true,
            'message' => '',
            'results' => [
                'subscriptions' => $subscriptions
            ]
        ];
        return response()->json($responseData);
    }

    public function show($id)
    {
        $user = Auth::user();

        $subscription = Subscription::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            return response()->json([
                'status'  => false,
                'message' => 'Subscription not found',
                'results' => null
            ], 404);
        }

        $responseData = [
            'status'  => true,
            'message' => '',
            'results' => [
                'subscription' => $subscription
            ]
        ];
        return response()->json($responseData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:membership_plans,id',
            'status' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $subscription = Subscription::create($data);
        return response()->json($subscription, 201);
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $request->validate([
            'plan_id' => 'sometimes|exists:membership_plans,id',
            'status' => 'sometimes|boolean',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
        ]);

        $subscription->update($request->all());
        return response()->json($subscription);
    }

    public function destroy($id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted successfully']);
    }

    public static function getUserPlanStatus()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)->first();

        if (!$subscription) {
            return response()->json([
                'status'  => false,
                'message' => 'No active subscription found',
                'results' => null
            ], 404);
        }

        return $subscription->status ? 'active' : 'inactive';
    }
}