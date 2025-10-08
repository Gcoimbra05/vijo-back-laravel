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
        return response()->json(PlatformText::all());
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
        ]);
        $text = PlatformText::create($validated);
        return response()->json($text, 201);
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
        return response()->json(['message' => 'Text deleted successfully']);
    }
}
