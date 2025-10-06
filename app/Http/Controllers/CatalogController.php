<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

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
        // Find all active catalogs in the category
        $categoryId = $this->catalog->category_id ?? 0;
        $allCatalogs = Catalog::where('is_deleted', 0)
            ->where('status', 1)
            ->where('category_id', $categoryId)
            ->get(['id', 'title', 'description', 'emoji', 'video_type_id']);

        // Count how many times each catalog was recorded by the user
        $catalogCounts = VideoRequest::where('user_id', $userId)
            ->whereIn('catalog_id', $allCatalogs->pluck('id'))
            ->selectRaw('catalog_id, COUNT(*) as count')
            ->groupBy('catalog_id')
            ->pluck('count', 'catalog_id');

        // Find the lowest number of recordings
        $minCount = $catalogCounts->count() ? $catalogCounts->min() : 0;

        // Filter catalogs that the user recorded the least number of times
        $leastRecordedCatalogs = $allCatalogs->filter(function($catalog) use ($catalogCounts, $minCount) {
            return ($catalogCounts[$catalog->id] ?? 0) == $minCount;
        });

        // Avoid recommending the same catalog that is currently being displayed
        $leastRecordedCatalogs = $leastRecordedCatalogs->filter(function($catalog) {
            return $catalog->id !== ($this->catalog->id ?? 0);
        });

        // If there are not at least 3, complete with the next least recorded catalogs
        if ($leastRecordedCatalogs->count() < 3) {
            $nextMinCount = $minCount + 1;
            $nextCatalogs = $allCatalogs->filter(function($catalog) use ($catalogCounts, $nextMinCount) {
            return ($catalogCounts[$catalog->id] ?? 0) == $nextMinCount;
            })->filter(function($catalog) {
            return $catalog->id !== ($this->catalog->id ?? 0);
            });
            $leastRecordedCatalogs = $leastRecordedCatalogs->concat($nextCatalogs)->take($limit);
        } else {
            $leastRecordedCatalogs = $leastRecordedCatalogs->take($limit);
        }

        // If there are still not 3, fetch from another category
        if ($leastRecordedCatalogs->count() < $limit) {
            $needed = $limit - $leastRecordedCatalogs->count();
            $otherCatalogs = Catalog::where('is_deleted', 0)
            ->where('status', 1)
            ->where('category_id', '<>', $categoryId)
            ->limit($needed)
            ->get(['id', 'title', 'description', 'emoji', 'video_type_id']);
            $leastRecordedCatalogs = $leastRecordedCatalogs->concat($otherCatalogs);
        }

        return $leastRecordedCatalogs->values();
    }
}