<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $membership_plans = MembershipPlan::where('status', 1)
            ->orderBy('admin_order', 'ASC')
            ->get(['id', 'name', 'description']);

        if ($membership_plans->isEmpty()) {
            $responseData = [
                'status'  => false,
                'message' => "No membership plans available.",
                'results' => [
                    'membership_plans' => []
                ]
            ];
        } else {
            $responseData = [
                'status'  => true,
                'message' => "",
                'results' => [
                    'membership_plans' => $membership_plans
                ]
            ];
        }

        return response()->json($responseData);
    }

    public function show($id)
    {
        $plan = MembershipPlan::find($id);

        if (!$plan) {
            $responseData = [
                'status'  => false,
                'message' => "Membership plan not found.",
                'results' => [
                    'membership_plan' => null
                ]
            ];
        } else {
            $responseData = [
                'status'  => true,
                'message' => "",
                'results' => [
                    'membership_plan' => $plan
                ]
            ];
        }

        return response()->json($responseData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'payment_mode' => 'required|boolean',
            'monthly_cost' => 'required|numeric',
            'annual_cost' => 'required|numeric',
            'payment_link' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $plan = MembershipPlan::create($request->all());

        if ($plan) {
            $responseData = [
                'status'  => true,
                'message' => "",
                'results' => [
                    'membership_plan' => $plan
                ]
            ];
        } else {
            $responseData = [
                'status'  => false,
                'message' => "Failed to create membership plan.",
                'results' => [
                    'membership_plan' => null
                ]
            ];
        }

        return response()->json($responseData, $plan ? 201 : 400);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|nullable|string|max:250',
            'payment_mode' => 'sometimes|required|boolean',
            'monthly_cost' => 'sometimes|required|numeric',
            'annual_cost' => 'sometimes|required|numeric',
            'payment_link' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|required|boolean',
        ]);

        $plan = MembershipPlan::find($id);

        if (!$plan) {
            $responseData = [
                'status'  => false,
                'message' => "Membership plan not found.",
                'results' => [
                    'membership_plan' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $plan->update($request->all());

        $responseData = [
            'status'  => true,
            'message' => "",
            'results' => [
                'membership_plan' => $plan
            ]
        ];

        return response()->json($responseData);
    }

    public function destroy($id)
    {
        $plan = MembershipPlan::find($id);

        if (!$plan) {
            $responseData = [
                'status'  => false,
                'message' => "Membership plan not found.",
                'results' => [
                    'membership_plan' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $plan->delete();

        $responseData = [
            'status'  => true,
            'message' => "Membership plan deleted successfully.",
            'results' => [
                'membership_plan' => null
            ]
        ];

        return response()->json($responseData, 200);
    }

    public static function getMembershipPlans()
    {
       $membershipPlans = MembershipPlan::where('status', 1)
            ->get(['id', 'slug', 'name as title', 'description', 'payment_link'])
            ->map(function ($plan) {
                return [
                    'id' => (string)$plan->id,
                    'slug' => $plan->slug,
                    'title' => $plan->title,
                    'description' => $plan->description,
                    'payment_link' => $plan->payment_link,
                ];
            })
            ->toArray();

        return $membershipPlans;
    }
}