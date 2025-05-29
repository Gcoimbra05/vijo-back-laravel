<?php

namespace App\Http\Controllers;

use App\Models\CatalogQuestion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogQuestionController extends Controller
{
    public function index()
    {
        $questions = CatalogQuestion::with('catalog')->get();
        return response()->json([
            'success' => true,
            'message' => 'Catalog questions retrieved successfully.',
            'data' => $questions,
        ]);
    }

    public function show($id)
    {
        $question = CatalogQuestion::with('catalog')->find($id);
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog question not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Catalog question retrieved successfully.',
            'data' => $question,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|integer|exists:catalogs,id',
            'reference_type' => 'nullable|integer',
            'metric1_title' => 'nullable|string|max:255',
            'metric1_question' => 'nullable|string|max:255',
            'metric1_question_option1' => 'nullable|string|max:50',
            'metric1_question_option2' => 'nullable|string|max:50',
            'metric1_question_option1val' => 'nullable|integer',
            'metric1_question_option2val' => 'nullable|integer',
            'metric1_question_label' => 'nullable|integer',
            'metric1_significance' => 'nullable|integer',
            'metric2_title' => 'nullable|string|max:255',
            'metric2_question' => 'nullable|string|max:255',
            'metric2_question_option1' => 'nullable|string|max:50',
            'metric2_question_option2' => 'nullable|string|max:50',
            'metric2_question_option1val' => 'nullable|integer',
            'metric2_question_option2val' => 'nullable|integer',
            'metric2_question_label' => 'nullable|integer',
            'metric2_significance' => 'nullable|integer',
            'metric3_title' => 'nullable|string|max:255',
            'metric3_question' => 'nullable|string|max:255',
            'metric3_question_option1' => 'nullable|string|max:50',
            'metric3_question_option2' => 'nullable|string|max:50',
            'metric3_question_option1val' => 'nullable|integer',
            'metric3_question_option2val' => 'nullable|integer',
            'metric3_question_label' => 'nullable|integer',
            'metric3_significance' => 'nullable|integer',
            'video_question' => 'nullable|string|max:255',
            'metric4_significance' => 'nullable|integer',
            'metric5_significance' => 'nullable|integer',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $question = CatalogQuestion::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Catalog question created successfully.',
            'data' => $question->load('catalog'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $question = CatalogQuestion::find($id);
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog question not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'catalog_id' => 'sometimes|required|integer|exists:catalogs,id',
            'reference_type' => 'nullable|integer',
            'metric1_title' => 'nullable|string|max:255',
            'metric1_question' => 'nullable|string|max:255',
            'metric1_question_option1' => 'nullable|string|max:50',
            'metric1_question_option2' => 'nullable|string|max:50',
            'metric1_question_option1val' => 'nullable|integer',
            'metric1_question_option2val' => 'nullable|integer',
            'metric1_question_label' => 'nullable|integer',
            'metric1_significance' => 'nullable|integer',
            'metric2_title' => 'nullable|string|max:255',
            'metric2_question' => 'nullable|string|max:255',
            'metric2_question_option1' => 'nullable|string|max:50',
            'metric2_question_option2' => 'nullable|string|max:50',
            'metric2_question_option1val' => 'nullable|integer',
            'metric2_question_option2val' => 'nullable|integer',
            'metric2_question_label' => 'nullable|integer',
            'metric2_significance' => 'nullable|integer',
            'metric3_title' => 'nullable|string|max:255',
            'metric3_question' => 'nullable|string|max:255',
            'metric3_question_option1' => 'nullable|string|max:50',
            'metric3_question_option2' => 'nullable|string|max:50',
            'metric3_question_option1val' => 'nullable|integer',
            'metric3_question_option2val' => 'nullable|integer',
            'metric3_question_label' => 'nullable|integer',
            'metric3_significance' => 'nullable|integer',
            'video_question' => 'nullable|string|max:255',
            'metric4_significance' => 'nullable|integer',
            'metric5_significance' => 'nullable|integer',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $question->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Catalog question updated successfully.',
            'data' => $question->load('catalog'),
        ]);
    }

    public function destroy($id)
    {
        $question = CatalogQuestion::find($id);
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog question not found.',
                'data' => null,
            ], 404);
        }
        $question->delete();
        return response()->json([
            'success' => true,
            'message' => 'Catalog question deleted successfully.',
            'data' => null,
        ]);
    }

    public static function getQuestionsByCatalogId($catalogId)
    {
        return CatalogQuestion::where('catalog_id', $catalogId)
            ->get(['id', 'reference_type', 'video_question'])
            ->toArray();
    }
}