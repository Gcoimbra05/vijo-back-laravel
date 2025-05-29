<?php

namespace App\Http\Controllers;

use App\Models\CatalogAnswer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CatalogAnswerController extends Controller
{
    public function index()
    {
        $answers = CatalogAnswer::with(['user', 'catalog', 'request'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Catalog answers retrieved successfully.',
            'data' => $answers,
        ]);
    }

    public function show($id)
    {
        $answer = CatalogAnswer::with(['user', 'catalog', 'request'])->find($id);
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog answer not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Catalog answer retrieved successfully.',
            'data' => $answer,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|integer|exists:catalogs,id',
            'request_id' => 'required|integer|exists:video_requests,id',
            'cred_score' => 'nullable|numeric',
            'metric1_answer' => 'nullable|string|max:50',
            'metric1Range' => 'nullable|numeric',
            'metric1Significance' => 'nullable|integer',
            'metric2_answer' => 'nullable|string|max:50',
            'metric2Range' => 'nullable|numeric',
            'metric2Significance' => 'nullable|integer',
            'metric3_answer' => 'nullable|string|max:50',
            'metric3Range' => 'nullable|numeric',
            'metric3Significance' => 'nullable|integer',
            'n8n_executionId' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $answer = CatalogAnswer::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Catalog answer created successfully.',
            'data' => [
                'request_id' => $answer->request_id,
                'record_category' => 0,
                'record_date' => $request->input('record_date', now()),
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $answer = CatalogAnswer::find($id);
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog answer not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'catalog_id' => 'sometimes|required|integer|exists:catalogs,id',
            'request_id' => 'sometimes|required|integer|exists:video_requests,id',
            'cred_score' => 'nullable|numeric',
            'metric1_answer' => 'nullable|string|max:50',
            'metric1Range' => 'nullable|numeric',
            'metric1Significance' => 'nullable|integer',
            'metric2_answer' => 'nullable|string|max:50',
            'metric2Range' => 'nullable|numeric',
            'metric2Significance' => 'nullable|integer',
            'metric3_answer' => 'nullable|string|max:50',
            'metric3Range' => 'nullable|numeric',
            'metric3Significance' => 'nullable|integer',
            'n8n_executionId' => 'nullable|string|max:50',
        ]);

        $answer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Catalog answer updated successfully.',
            'data' => $answer->load(['user', 'catalog', 'request']),
        ]);
    }

    public function destroy($id)
    {
        $answer = CatalogAnswer::find($id);
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog answer not found.',
                'data' => null,
            ], 404);
        }
        $answer->delete();
        return response()->json([
            'success' => true,
            'message' => 'Catalog answer deleted successfully.',
            'data' => null,
        ]);
    }
}