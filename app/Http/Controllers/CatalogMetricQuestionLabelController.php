<?php

namespace App\Http\Controllers;

use App\Models\CatalogMetricQuestionLabel;
use Illuminate\Http\Request;

class CatalogMetricQuestionLabelController extends Controller
{
    public function index()
    {
        return response()->json(CatalogMetricQuestionLabel::all());
    }

    public function show($id)
    {
        $label = CatalogMetricQuestionLabel::find($id);
        if (!$label) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($label);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'metricOption1Emoji' => 'nullable|string|max:100',
            'metricOption1Text' => 'nullable|string|max:100',
            'metricOption3Emoji' => 'nullable|string|max:100',
            'metricOption3Text' => 'nullable|string|max:100',
            'metricOption5Emoji' => 'nullable|string|max:100',
            'metricOption5Text' => 'nullable|string|max:100',
            'metricOption7Emoji' => 'nullable|string|max:100',
            'metricOption7Text' => 'nullable|string|max:100',
            'metricOption9Emoji' => 'nullable|string|max:100',
            'metricOption9Text' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        $label = CatalogMetricQuestionLabel::create($data);
        return response()->json($label, 201);
    }

    public function update(Request $request, $id)
    {
        $label = CatalogMetricQuestionLabel::find($id);
        if (!$label) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'metricOption1Emoji' => 'nullable|string|max:100',
            'metricOption1Text' => 'nullable|string|max:100',
            'metricOption3Emoji' => 'nullable|string|max:100',
            'metricOption3Text' => 'nullable|string|max:100',
            'metricOption5Emoji' => 'nullable|string|max:100',
            'metricOption5Text' => 'nullable|string|max:100',
            'metricOption7Emoji' => 'nullable|string|max:100',
            'metricOption7Text' => 'nullable|string|max:100',
            'metricOption9Emoji' => 'nullable|string|max:100',
            'metricOption9Text' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        $label->update($data);
        return response()->json($label);
    }

    public function destroy($id)
    {
        $label = CatalogMetricQuestionLabel::find($id);
        if (!$label) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $label->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}