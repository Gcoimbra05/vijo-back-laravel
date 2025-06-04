<?php

namespace App\Http\Controllers;

use App\Models\CatalogMetricQuestionLabel;
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

    public static function getQuestionsByCatalogId($catalog, $vtKpiMetrics)
    {
        $fields = ['id', 'reference_type', 'video_question'];

        if ($vtKpiMetrics >= 1) {
            $fields = array_merge($fields, [
                'metric1_title',
                'metric1_question',
                'metric1_question_option1',
                'metric1_question_option2',
                'metric1_question_option1val',
                'metric1_question_option2val',
                'metric1_question_label',
            ]);
        }

        if ($vtKpiMetrics == 3) {
            $fields = array_merge($fields, [
                'metric2_title',
                'metric2_question',
                'metric2_question_option1',
                'metric2_question_option2',
                'metric2_question_option1val',
                'metric2_question_option2val',
                'metric2_question_label',
                'metric3_title',
                'metric3_question',
                'metric3_question_option1',
                'metric3_question_option2',
                'metric3_question_option1val',
                'metric3_question_option2val',
                'metric3_question_label',
            ]);
        }

        $question = CatalogQuestion::where('catalog_id', $catalog->id)
            ->where('status', 1)
            ->select($fields)
            ->first();
        $questions = $question ? $question->toArray() : [];

        foreach ($questions as $k => $r) {
            // Métrica 1
            $metric1_question_label = $r['metric1_question_label'] ?? 0;
            if ($metric1_question_label > 0) {
                $label = CatalogMetricQuestionLabel::where('status', 1)->find($metric1_question_label);
                if ($label) {
                    $metric1_question_labels = [];
                    foreach ([1, 3, 5, 7, 9] as $option) {
                        $metric1_question_labels[] = [
                            'emoji' => $label->{'metricOption' . $option . 'Emoji'},
                            'text' => $label->{'metricOption' . $option . 'Text'},
                            'question_emoji' => $label->{'metricOption' . $option . 'Emoji'},
                            'value' => $option
                        ];
                    }
                    $questions[$k]['metric1_question_labels'] = $metric1_question_labels;
                }
            }

            // Métrica 2 e 3 só se kpi_metrics == 3
            if ($vtKpiMetrics == 3) {
                // Métrica 2
                $metric2_question_label = $r['metric2_question_label'] ?? 0;
                if ($metric2_question_label > 0) {
                    $label = CatalogMetricQuestionLabel::where('status', 1)->find($metric2_question_label);
                    if ($label) {
                        $metric2_question_labels = [];
                        foreach ([1, 3, 5, 7, 9] as $option) {
                            $metric2_question_labels[] = [
                                'emoji' => $label->{'metricOption' . $option . 'Emoji'},
                                'text' => $label->{'metricOption' . $option . 'Text'},
                                'question_emoji' => $label->{'metricOption' . $option . 'Emoji'},
                                'value' => $option
                            ];
                        }
                        $questions[$k]['metric2_question_labels'] = $metric2_question_labels;
                    }
                }

                // Métrica 3
                $metric3_question_label = $r['metric3_question_label'] ?? 0;
                if ($metric3_question_label > 0) {
                    $label = CatalogMetricQuestionLabel::where('status', 1)->find($metric3_question_label);
                    if ($label) {
                        $metric3_question_labels = [];
                        foreach ([1, 3, 5, 7, 9] as $option) {
                            $metric3_question_labels[] = [
                                'emoji' => $label->{'metricOption' . $option . 'Emoji'},
                                'text' => $label->{'metricOption' . $option . 'Text'},
                                'question_emoji' => $label->{'metricOption' . $option . 'Emoji'},
                                'value' => $option
                            ];
                        }
                        $questions[$k]['metric3_question_labels'] = $metric3_question_labels;
                    }
                }
            }
        }

        return $questions;
    }
}