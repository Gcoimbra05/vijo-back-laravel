<?php

namespace App\Http\Controllers;

use App\Models\VideoType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VideoTypeController extends Controller
{
    public function index()
    {
        $types = VideoType::all();
        return response()->json([
            'success' => true,
            'message' => 'Video types retrieved successfully.',
            'data' => $types,
        ]);
    }

    public function show($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Video type retrieved successfully.',
            'data' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'kpi_no' => 'required|integer',
            'metric_no' => 'required|integer',
            'video_no' => 'required|integer',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $type = VideoType::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Video type created successfully.',
            'data' => $type,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:100',
            'kpi_no' => 'sometimes|required|integer',
            'metric_no' => 'sometimes|required|integer',
            'video_no' => 'sometimes|required|integer',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $type->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Video type updated successfully.',
            'data' => $type,
        ]);
    }

    public function destroy($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }
        $type->delete();
        return response()->json([
            'success' => true,
            'message' => 'Video type deleted successfully.',
            'data' => null,
        ]);
    }
}