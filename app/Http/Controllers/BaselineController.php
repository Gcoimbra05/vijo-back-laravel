<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Baseline;
use Carbon\Carbon;

class BaselineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $baselines = Baseline::where('user_id', $userId)->get();
        return response()->json([
            'success' => true,
            'message' => 'Baselines retrieved successfully.',
            'data' => $baselines,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'starts_at' => 'date_format:Y-m-d H:i:s',
            'ends_at' => 'date_format:Y-m-d H:i:s'
        ]);

        if (!$request->starts_at) $request['starts_at'] = Carbon::now();

        $request['user_id'] = $userId;
        $baseline = Baseline::create($request->all());
        

        return response()->json([
            'success' => true,
            'message' => 'Baseline created successfully.',
            'data' => $baseline,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $baseline = Baseline::where('user_id', $userId)->find($id);
        if (!$baseline) {
            return response()->json([
                'success' => false,
                'message' => 'Baseline not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Baseline retrieved successfully.',
            'data' => $baseline,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $baseline = Baseline::where('user_id', $userId)->find($id);
        if (!$baseline) {
            return response()->json([
                'success' => false,
                'message' => 'Baseline not found.',
                'data' => null,
            ], 404);
        }
        
        $baseline->delete();

        return response()->json([
            'success' => true,
            'message' => 'Baseline deleted successfully.',
            'data' => null,
        ]);
    }
}
