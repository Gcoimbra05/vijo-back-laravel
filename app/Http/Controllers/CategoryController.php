<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('catalogs')->get();
        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'data' => $categories,
        ]);
    }

    public function show($id)
    {
        $category = Category::with('catalogs')->find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'emoji' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1,2',
        ]);

        $category = Category::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category->load('catalogs'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'emoji' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1,2',
        ]);

        $category->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $category->load('catalogs'),
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data' => null,
            ], 404);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
            'data' => null,
        ]);
    }

    public static function getCategories()
    {
        return Category::all()->map(function ($category) {
            return [
                "id" => (string)$category->id,
                "name" => $category->name,
                "description" => $category->description,
                "category_emoji" => $category->emoji,
                "emoji" => $category->emoji,
            ];
        })->toArray();

        /* [
                [
                    "id" => "1",
                    "name" => "Life Stories",
                    "description" => "Capture the moments that shaped you — from cherished memories to life’s defining chapters. Reflect, remember, and relive what matters most — and share your journey with those who matter most.",
                    "category_emoji" => "U+1F333",
                    "emoji" => "https://vijo.com/uploads/medias/2167413647.png"
                ],
                [
                    "id" => "2",
                    "name" => "Emotional Check ",
                    "description" => "Pause to reflect, express, and grow. Tune into your feelings with gratitude and grace, building emotional awareness and resilience one check-in at a time.",
                    "category_emoji" => "U+1F64C",
                    "emoji" => "https://vijo.com/uploads/medias/2667203340.png"
                ],
                [
                    "id" => "3",
                    "name" => "Personal Growth",
                    "description" => "Celebrate progress, embrace challenges, and become the best version of yourself. This is your space for self-discovery, learning, and meaningful change — one step at a time.",
                    "category_emoji" => "U+1F331",
                    "emoji" => "https://vijo.com/uploads/medias/5341288882.png"
                ]
            ] */
    }
}