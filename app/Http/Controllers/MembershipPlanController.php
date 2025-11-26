<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $memberships = MembershipPlan::orderBy('id', 'desc')->get();
        $pageTitle = 'Membership Plans';
        $nav_bar = 'Memberships Plans';
        $breadcrumbs = [
            ['label' => 'Memberships Plans', 'url' => null],
        ];

        return view('admin.memberships.list', compact('memberships', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    public function add()
    {
        $pageTitle = "Add Membership Plans";
        $nav_bar = "Memberships Plans";
        $breadcrumbs = [
            ['label' => 'Memberships', 'url' => route('membership.index')],
            ['label' => 'Add Membership Plan', 'url' => null],
        ];

        return view('admin.memberships.form', [
            'action' => 'Add',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [],
        ]);
    }

    public function show($id)
    {
        $info = MembershipPlan::findOrFail($id);
        $action = 'Edit';

        $pageTitle = "Edit Membership Plan";
        $nav_bar = "Membership Plans";
        $breadcrumbs = [
            ['label' => 'Memberships', 'url' => route('membership.index')],
            ['label' => 'Edit Membership Plan', 'url' => null],
        ];

        return view('admin.memberships.form', [
            'info' => [$info],
            'action' => $action,
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'payment_mode' => 'required|boolean',
            'monthly_cost' => 'required|numeric',
            'annual_cost' => 'required|numeric',
            'payment_link' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $plan = MembershipPlan::create($validated);

        if ($plan) {
            return redirect()->route('membership.index')->with('success', 'Membership Plan added successfully.');
        }

        return redirect()->route('membership.index')->with('error', 'Failed to create membership plan.');
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
            return response()->json(['status' => false, 'message' => "Membership plan not found."], 404);
        }

        $plan->update($request->all());

        return redirect()->route('membership.index')
            ->with('success', 'Membership Plan edited successfully.');
    }

    public function destroy($id)
    {
        $plan = MembershipPlan::find($id);

        if (!$plan) {
            return response()->json(['status' => false, 'message' => "Membership plan not found."], 404);
        }

        $plan->delete();

        return redirect()->route('membership.index')->with('success', 'Membership Plan deleted successfully.');
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

    public function deactivate($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $plan->status = 0;
        $plan->save();

        return redirect()->route('membership.index')->with('success', 'Membership plan deactivated successfully.');
    }

    public function activate($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $plan->status = 1;
        $plan->save();

        return redirect()->route('membership.index')->with('success', 'Membership plan activated successfully.');
    }
}
