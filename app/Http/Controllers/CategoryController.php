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
    }
}