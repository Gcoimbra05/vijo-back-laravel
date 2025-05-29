<?php

namespace App\Http\Controllers;

use App\Models\LlmTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LlmTemplateController extends Controller
{
    /**
     * Get all templates for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
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
            'success' => true,
            'message' => 'Llm templates retrieved successfully.',
            'data' => $llmTemplates,
            'meta' => [
                'total' => LlmTemplate::countUserTemplates($userId),
                'per_page' => $perPage,
                'current_page' => $page,
            ]
        ]);
    }

    /**
     * Get a specific template for the authenticated user.
     */
    public function show($id)
    {
        $llmTemplate = LlmTemplate::findUserTemplate($id, Auth::id());
        
        if (!$llmTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Llm template not found.',
                'data' => null,
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Llm template retrieved successfully.',
            'data' => $llmTemplate,
        ]);
    }

    /**
     * Create a new template for the authenticated user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'llm' => 'required|string|max:10',
            'system_prompt' => 'required|string',
            'examples' => 'required|string',
            'llm_temperature' => 'required|numeric|between:0.1,1',
            'llm_response_max_length' => 'required|integer|max:5000'
        ]);

        $llmTemplate = LlmTemplate::createForUser(
            Auth::id(),
            $validated['name'],
            $validated['llm'],
            $validated['system_prompt'],
            $validated['examples'],
            $validated['llm_temperature'],
            $validated['llm_response_max_length'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Llm template created successfully.',
            'data' => ['id' => $llmTemplate->id],
        ], 201);
    }

    /**
     * Update a template for the authenticated user.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'system_prompt' => 'required|string',
            'llm' => 'required|string|max:10',
        ]);

        $updated = LlmTemplate::updateUserTemplate($id, Auth::id(), $validated);
        
        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Llm template not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Llm template updated successfully.',
            'data' => ['id' => (int) $id],
        ]);
    }

    /**
     * Delete a template for the authenticated user.
     */
    public function destroy($id)
    {
        $deleted = LlmTemplate::deleteUserTemplate($id, Auth::id());
        
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Llm template not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Llm template deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Get templates by LLM type for the authenticated user.
     */
    public function getByLlm(Request $request, $llmType)
    {
        $templates = LlmTemplate::getTemplatesByLlm(Auth::id(), $llmType);

        return response()->json([
            'success' => true,
            'message' => "Templates for {$llmType} retrieved successfully.",
            'data' => $templates,
        ]);
    }
}