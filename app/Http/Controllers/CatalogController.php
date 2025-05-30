<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    public function index()
    {
        $catalogs = Catalog::with(['category'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Catalogs retrieved successfully.',
            'data' => $catalogs,
        ]);
    }

    public function show($id)
    {
        $catalog = Catalog::with(['category'])->find($id);
        if (!$catalog) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Catalog retrieved successfully.',
            'data' => $catalog,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'min_record_time' => 'required|integer|min:1',
            'max_record_time' => 'required|integer|max:30',
            'emoji' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1,2,3',
            'parent_catalog_id' => 'nullable|integer|exists:catalogs,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'is_promotional' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
        ]);

        $catalog = Catalog::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Catalog created successfully.',
            'data' => $catalog->load(['category']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'min_record_time' => 'sometimes|required|integer|min:1',
            'max_record_time' => 'sometimes|required|integer|max:30',
            'emoji' => 'nullable|string|max:100',
            'status' => 'sometimes|required|integer|in:0,1,2,3',
            'parent_catalog_id' => 'nullable|integer|exists:catalogs,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'is_promotional' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
        ]);

        $catalog->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Catalog updated successfully.',
            'data' => $catalog->load(['category']),
        ]);
    }

    public function destroy($id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog not found.',
                'data' => null,
            ], 404);
        }
        $catalog->delete();
        return response()->json([
            'success' => true,
            'message' => 'Catalog deleted successfully.',
            'data' => null,
        ]);
    }

    public function getCatalogsByCategory($categoryId)
    {
        $catalogs = Catalog::where('category_id', $categoryId)->with(['category'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Catalogs by category retrieved successfully.',
            'data' => $catalogs,
        ]);
    }
}