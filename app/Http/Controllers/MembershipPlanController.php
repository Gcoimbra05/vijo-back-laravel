<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $membership_plans = MembershipPlan::where('status', 1)
            ->get(['id', 'name', 'description', 'slug']);

        if ($membership_plans->isEmpty()) {
            $responseData = [
                'status'  => false,
                'message' => "No membership plans available.",
                'results' => [
                    'membership_plans' => []
                ]
            ];
        } 
         $membership_plans = MembershipPlan::where('status', 1)
        ->get(['id', 'name', 'description', 'slug']);

        $memberships = MembershipPlan::all(); // pega todos os planos

        $pageTitle = 'Membership Plans';
        $nav_bar = 'Memberships Plans';
        $breadcrumbs = [
            ['label' => 'Memberships Plans', 'url' => null],
        ];

        return view('admin.memberships.list', compact('memberships', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    public function add()
    {
        Log::info('Membership-PlanController@create chamado');
        $pageTitle = "Add Mebership Plans";
        $nav_bar = "Memberships Plans";
        $breadcrumbs = [
            ['label' => 'membership', 'url' => route('membership.index')],
            ['label' => 'Add Membership Plan', 'url' => null],
        ];
        $memberships = MembershipPlan::all();
          

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
        // Validação dos dados
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:250',
            'payment_mode' => 'required|boolean',
            'monthly_cost' => 'required|numeric',
            'annual_cost' => 'required|numeric',
            'payment_link' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Criar o plano de membership
        // O slug será gerado automaticamente pelo mutator no modelo
        $plan = MembershipPlan::create($validated);

        // Preparar resposta JSON
        $responseData = $plan
            ? [
                'status'  => true,
                'message' => 'Membership plan created successfully.',
                'results' => ['membership_plan' => $plan]
            ]
            : [
                'status'  => false,
                'message' => 'Failed to create membership plan.',
                'results' => ['membership_plan' => null]
            ];

        // Redireciona para a lista de memberships
        return redirect()->route('membership.index')
        ->with('success', 'Membership Plan created successfully.');
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

        return redirect()->route('membership.index')
        ->with('success', 'Membership Plan edited successfully.');
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

    public function edit($id)
    {
        $info = MembershipPlan::findOrFail($id); // pega o registro ou retorna 404
        $action = 'Edit';

        $pageTitle = "Edit Membership Plan";
        $nav_bar = "Membership Plans";
        $breadcrumbs = [
            ['label' => 'Memberships', 'url' => route('membership.index')],
            ['label' => 'Edit Membership Plan', 'url' => null],
        ];

        return view('admin.memberships.form', [
            'info' => [$info], // seu Blade espera um array no index 0
            'action' => $action,
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

}
