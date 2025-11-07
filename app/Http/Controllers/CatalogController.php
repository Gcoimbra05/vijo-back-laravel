<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\SkipVijo;
use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    protected $catalog;

    public function __construct(?Catalog $catalog = null)
    {
        $this->catalog = $catalog;
    }

    public function index()
    {
        $catalogs = Catalog::with(['category'])->where('is_deleted', 0)->get();
        return response()->json([
            'success' => true,
            'message' => 'Catalogs retrieved successfully.',
            'data' => $catalogs,
        ]);
    }

    public function show($id)
    {
        $catalog = Catalog::with(['category'])->where('id', $id)->where('is_deleted', 0)->first();
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
        $catalog = Catalog::where('id', $id)->where('is_deleted', 0)->first();
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
        $catalogs = Catalog::where('category_id', $categoryId)->where('is_deleted', 0)->get();

        return response()->json([
            'success' => true,
            'message' => 'Catalogs by category retrieved successfully.',
            'data' => $catalogs,
        ]);
    }

    public function getSuggestedCatalogs($userId = null, $limit = 3)
    {
        $categoryId = $this->catalog->category_id ?? 0;
        $today = now()->toDateString();

        // Get catalogs skipped by the user today
        $skippedCatalogIds = SkipVijo::where('user_id', $userId)
            ->whereDate('skipped_at', $today)
            ->pluck('catalog_id')
            ->toArray();

        // Get all active catalogs in the category, not skipped today
        $catalogs = Catalog::where('is_deleted', 0)
            ->where('status', 1)
            ->where('category_id', $categoryId)
            ->whereNotIn('id', $skippedCatalogIds)
            ->get(['id', 'title', 'description', 'emoji', 'video_type_id', 'category_id']);

        // Get usage count for each catalog today
        $usageCounts = VideoRequest::where('user_id', $userId)
            ->whereIn('catalog_id', $catalogs->pluck('id'))
            ->whereDate('created_at', $today)
            ->selectRaw('catalog_id, COUNT(*) as count')
            ->groupBy('catalog_id')
            ->pluck('count', 'catalog_id');

        // Sort catalogs by least used first
        $sortedCatalogs = $catalogs->sortBy(function($catalog) use ($usageCounts) {
            return $usageCounts[$catalog->id] ?? 0;
        })->values();

        return $sortedCatalogs->take($limit);
    }
}