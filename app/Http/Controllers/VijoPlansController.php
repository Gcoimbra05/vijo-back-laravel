<?php

namespace App\Http\Controllers;

use App\Services\VijoPlansService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB ;
use App\Models\Catalog;
use App\Models\VijoPlan;

class VijoPlansController {

    public function __construct(protected VijoPlansService $vijoPlansService){}

    public function index(Request $request)
    {
        $user = Auth::user() ?? (object)['id' => 14];
        $editId = $request->edit ?? null;
        $vijo_plans = VijoPlan::where('user_id', $user->id)->get();
        $editing = $editId ? VijoPlan::find($editId) : null;
        $action = $editing ? 'Edit' : 'Create';
        $pageTitle = 'Vijo Plans';
        $nav_bar = 'Vijo Plans';
        $breadcrumbs = [
            ['label' => 'Vijo Plans', 'url' => null],
        ];

        return view('admin.vijoplans.form', compact('vijo_plans', 'editing', 'action', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }



    /*public function plansPage()
    {
        $user = Auth::user();

        $vijo_plans = VijoPlan::where('user_id', $user->id)->get();

        return view('insights.plans', compact('vijo_plans'));
    }*/

    public function show($vijoPlanId)
    {
        $user = Auth::user();

        $vijoPlan = VijoPlan::select('id', 'user_id', 'name', 
            'description', 'length_in_weeks', 'created_at', 
            'updated_at')
            ->where('user_id', $user?->id)
            ->where('id', $vijoPlanId)
            ->with('catalogs.videoRequests')
            ->first();

        $this->vijoPlansService->markCatalogsAsRecorded($user, $vijoPlan);

        return response()->json([
            'success' => true,
            'message' => 'Vijo plan fetched successfully.',
            'data' => $vijoPlan,
        ], 201);
    }

    public function store (Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.',
                'results' => [
                    'emlo_responses' => null
                ],
            ], 401);
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'length_in_weeks' => 'required|integer',
            'sub_vijos' => 'required|array'
        ]);

        $allowedModalities = [
            "ACT",
            "Art Therapy",
            "Behavioral Activation",
            "CBT",
            "Coaching/Wheel of Life",
            "DBT",
            "Emotion-Focused",
            "Expressive Writing",
            "Family Systems",
            "Humanistic",
            "Mindfulness",
            "Motivational Interviewing",
            "Narrative Therapy",
            "Positive Psychology",
            "Reminiscence",
            "Somatic"
        ];

        $allowedFrequenciesOfReviewing = [
            "After conflict",
            "After crises",
            "After difficult moments",
            "After emotional events",
            "After new experiences",
            "After progress",
            "After setbacks",
            "After therapy/events",
            "After tough weeks",
            "Anniversaries",
            "Before challenges",
            "Each Sunday",
            "End of month review",
            "End of week review",
            "Goal review/check-in",
            "Gratitude review",
            "Group debrief",
            "Health milestones",
            "Holiday/event",
            "Monthly inspiration",
            "Monthly review",
            "Reflective occasions",
            "Special occasions",
            "Start of next week",
            "Teach others",
            "Weekly review",
            "Year-in-review"
        ];

        $request->validate([
            'sub_vijos' => 'required|array|size:7', // Must be array with exactly 7 items
            'sub_vijos.*.name' => 'required|string',
            'sub_vijos.*.category_id' => 'required|integer|in:1,2,3',
            'sub_vijos.*.video_type_id' => 'required|integer|in:1,3',
            'sub_vijos.*.title' => 'required|string',
            'sub_vijos.*.message' => 'required|string',
            'sub_vijos.*.description' => 'required|string',
            'sub_vijos.*.emoji' => 'required|string',

            'sub_vijos.*.primary_modality' => ['required', 'string', Rule::in($allowedModalities)],
            'sub_vijos.*.secondary_modality' => ['required', 'string', Rule::in($allowedModalities)],
            'sub_vijos.*.tertiary_modality' => ['required', 'string', Rule::in($allowedModalities)],

            'sub_vijos.*.best_time_of_day_to_record' => 'required|string|in:Morning,Afternoon,Evening',
            'sub_vijos.*.frequency_of_recording' => 'required|string|in:Daily,Weekly,Biweekly,Monthly,As inspired',
            'sub_vijos.*.frequency_of_reviewing' => ['required', 'string', Rule::in($allowedFrequenciesOfReviewing)],

        ]);

        $data = $request->only([
            'name',
            'description',
            'length_in_weeks',
        ]);
        $data['ai_json'] = json_encode($request->all());
        $data['user_id'] = $userId;

        $vijoPlan = $this->vijoPlansService->createVijoPlan($data);

        Log::info('data type of the subvijos array is: ' . gettype($request->sub_vijos));
        Log::info('data structure of the subvijos array is: ' . json_encode($request->sub_vijos));

        
        foreach ($request->sub_vijos as $vijoIndex => $vijo) {
            Catalog::create(
                [
                    'name' => $vijo['name'],
                    'category_id' => $vijo['category_id'],
                    'video_type_id' => $vijo['video_type_id'],
                    'title' => $vijo['title'],
                    'message' => $vijo['message'],
                    'description' => $vijo['description'],
                    'emoji' => $vijo['emoji'],
                    'primary_modality' => $vijo['primary_modality'],
                    'secondary_modality' => $vijo['secondary_modality'],
                    'tertiary_modality' => $vijo['tertiary_modality'],
                    'best_time_of_day_to_record' => $vijo['best_time_of_day_to_record'],
                    'frequency_of_recording' => $vijo['frequency_of_recording'],
                    'frequency_of_reviewing' => $vijo['frequency_of_reviewing'],
                    'vijo_plan_id' => $vijoPlan->id,
                    'vijo_plan_order' => $vijoIndex
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Vijo plan created successfully.',
            'data' => $vijoPlan,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $vijoPlan = VijoPlan::find($id);

        if (!$vijoPlan) {
            return redirect()->back()->with('error', 'Vijo plan not found.');
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'length_in_weeks' => 'required|integer'
        ]);

        $vijoPlan->update($request->all());

        return redirect()
            ->route('users.edit', ['user' => $vijoPlan->user_id])
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $vijoPlan = VijoPlan::find($id);
        if (!$vijoPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Vijo plan not found.',
                'data' => null,
            ], 404);
        }
        $vijoPlan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Vijo plan deleted successfully.',
            'data' => null,
        ]);
    }
}