<?php

namespace App\Http\Controllers;

use App\Models\LlmTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LlmTemplateController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.',
                'results' => [
                    'llm_templates' => null
                ],
            ], 401);
        }

        $search = $request->query('search');
        $perPage = $request->query('limit', 15);
        $page = (int) $request->query('page', 1);
        $startAt = $perPage * ($page - 1);

        if ($search) {
            $llmTemplates = LlmTemplate::searchUserTemplates($userId, $search, $perPage);
        } else {
            $llmTemplates = LlmTemplate::getTemplatesByUser($userId, $startAt, $perPage);
        }

        return response()->json([
            'status' => true,
            'message' => 'Llm templates retrieved successfully.',
            'results' => [
                    'llm_templates' => $llmTemplates
            ],
        ]);
    }

    public function show($id)
    {
        $llmTemplate = LlmTemplate::findUserTemplate($id, Auth::id());
        
        if (!$llmTemplate) {
            $responseData = [
                'status'  => false,
                'message' => "Llm template not found.",
                'results' => [
                    'llm_template' => null
                ]
            ];
        } else {
            $responseData = [
                'status'  => true,
                'message' => "Llm template retrieved successfully.",
                'results' => [
                    'llm_template' => $llmTemplate
                ]
            ];
        }

        return response()->json($responseData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'llm' => 'required|string|max:10',
            'system_prompt' => 'required|string',
            'examples' => 'required|string',
            'llm_temperature' => 'required|numeric|between:0.1,1',
            'llm_response_max_length' => 'required|integer|max:5000'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $llmTemplate = LlmTemplate::create($data);

        if($llmTemplate){
            $responseData = [
                'status'  => true,
                'message' => "Llm template created successfully.",
                'results' => [
                    'llm_template' => $llmTemplate
                ]
            ];
        } else {
            $responseData = [
                'status'  => false,
                'message' => "Failed to create llm template.",
                'results' => [
                    'llm_template' => null
                ]
            ];
        }

        return response()->json($responseData, $llmTemplate ? 201 : 400);
        
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'system_prompt' => 'sometimes|string',
            'llm' => 'sometimes|string|max:10',
        ]);

        $llmTemplate = LlmTemplate::find($id);

        if (!$llmTemplate) {
            $responseData = [
                'status'  => false,
                'message' => "Llm template not found.",
                'results' => [
                    'llm_template' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $llmTemplate->update($request->all());

        $responseData = [
            'status'  => true,
            'message' => "Llm template updated successfully.",
            'results' => [
                'llm_template' => $llmTemplate
            ]
        ];

        return response()->json($responseData);
    }

    public function destroy($id)
    {
        $llmTemplate = LlmTemplate::find($id);

        if (!$llmTemplate) {
            $responseData = [
                'status'  => false,
                'message' => "Llm template not found.",
                'results' => [
                    'llm_template' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $llmTemplate->delete();

        $responseData = [
            'status'  => true,
            'message' => "Llm template deleted successfully.",
            'results' => [
                'llm_template' => null
            ]
        ];

        return response()->json($responseData, 200);
    }
}