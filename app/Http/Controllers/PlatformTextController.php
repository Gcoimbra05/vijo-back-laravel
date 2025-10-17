<?php

namespace App\Http\Controllers;

use App\Models\PlatformText;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlatformTextController extends Controller
{
    // List all platform texts
    public function index()
    {
        $platformtexts = PlatformText::orderBy('id', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Platform Texts retrieved successfully.',
                'data' => $platformtexts,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'Platform Texts', 'url' => null],
        ];

        $nav_bar = 'platformtext';
        $pageTitle = 'Platform Texts';

        return view('admin.platformtext.list', compact('platformtexts', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

     public function create()
    {
        $pageTitle = "Add Platform Text";
        $nav_bar = "platformtext";
        $breadcrumbs = [
            ['label' => 'Platform Texts', 'url' => route('platformtext.index')],
            ['label' => 'Add Platform Text', 'url' => null],
        ];

        return view('admin.platformtext.form', [
            'action' => 'create',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'platformText' => null,
        ]);
    }

    // Show a single platform text by id
    public function show($id)
    {
        $text = PlatformText::find($id);
        if (!$text) {
            return response()->json(['message' => 'Text not found'], 404);
        }
        return response()->json($text);
    }

    // Create a new platform text
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|unique:platform_texts,slug',
            'highlight' => 'nullable|string',
            'emoji' => 'nullable|string|max:16',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);
        PlatformText::create($validated);

        return redirect()->route('platformtext.index')
            ->with('success', 'Platform text created successfully.');
    }

    // Update an existing platform text
    public function update(Request $request, $id)
    {
        $text = PlatformText::find($id);
        if (!$text) {
            return response()->json(['message' => 'Text not found'], 404);
        }
        $validated = $request->validate([
            'slug' => 'sometimes|required|string|unique:platform_texts,slug,' . $id,
            'highlight' => 'nullable|string',
            'emoji' => 'nullable|string|max:16',
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
            'location' => 'nullable|string',
        ]);
        $text->update($validated);
        return response()->json($text);
    }

    // Delete a platform text
    public function destroy($id)
    {
        $text = PlatformText::find($id);
        if (!$text) {
            return response()->json(['message' => 'Text not found'], 404);
        }
        $text->delete();
        return redirect()->route('platformtext.index')
            ->with('success', 'Platform text created successfully.');
    }

    public function activate($id)
    {
        $template = platformtext::findOrFail($id);
        $template->status = 1; // ativo
        $template->save();

        return redirect()->route('emailtemplate.index')
                        ->with('success', 'Email Template activated successfully.');
    }

    public function deactivate($id)
    {
        $template = platformtext::findOrFail($id);
        $template->status = 0; // desativado
        $template->save();

        return redirect()->route('emailtemplate.index')
                        ->with('success', 'Email Template deactivated successfully.');
    }
}
