<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::with(['category', 'creator'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Tags retrieved successfully.',
            'data' => $tags,
        ]);
    }

    public function show($id)
    {
        $tag = Tag::with(['category', 'creator'])->find($id);
        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Tag retrieved successfully.',
            'data' => $tag,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:catalog_categories,id',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:catalog,journalTag,custom',
            'created_by_user' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $tag = Tag::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Tag created successfully.',
            'data' => $tag->load(['category', 'creator']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'category_id' => 'sometimes|required|integer|exists:catalog_categories,id',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:catalog,journalTag,custom',
            'created_by_user' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $tag->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Tag updated successfully.',
            'data' => $tag->load(['category', 'creator']),
        ]);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found.',
                'data' => null,
            ], 404);
        }
        $tag->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully.',
            'data' => null,
        ]);
    }

    public static function handleProvidedTags($tags, $categoryId = null)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $userId = Auth::id() ?? 0;
        $tagIds = [];
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (empty($tag)) {
                continue;
            }

            // Expected format "id##name"
            $tagParts = explode('##', $tag, 2);
            $tagId = isset($tagParts[0]) ? $tagParts[0] : null;
            $tagTitle = isset($tagParts[1]) ? $tagParts[1] : null;

            if ($tagId == 0 && $tagTitle) {
                $newTag = Tag::create([
                    'category_id'     => $categoryId,
                    'name'            => $tagTitle,
                    'type'            => 'custom',
                    'created_by_user' => $userId,
                    'status'          => 1,
                ]);
                $tagIds[] = $newTag->id;
            } elseif ($tagId > 0) {
                $tagIds[] = $tagId;
            }
        }

        return $tagIds;
    }
}