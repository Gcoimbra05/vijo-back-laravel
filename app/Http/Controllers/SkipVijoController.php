<?php

namespace App\Http\Controllers;

use App\Models\SkipVijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkipVijoController extends Controller
{
    /**
     * Store a skipped vijo for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|integer|exists:catalogs,id',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'User not authenticated.'], 401);
        }

        $skipVijo = SkipVijo::create([
            'user_id'    => $userId,
            'catalog_id' => $request->catalog_id,
            'skipped_at' => now()->toDateString(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vijo skipped and saved successfully.',
            'results' => $skipVijo
        ], 201);
    }
}
