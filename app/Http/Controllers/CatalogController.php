<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\SkipVijo;
use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\VideoType;
use App\Models\Category;
use App\Models\Tag;

class CatalogController extends Controller
{
    protected $catalog;

    public function __construct(?Catalog $catalog = null)
    {
        $this->catalog = $catalog;
    }

    public function index(Request $request)
    {
        $catalogs = Catalog::with(['category'])->where('is_deleted', 0)->orderBy('admin_order', 'asc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalogs retrieved successfully.',
                'data' => $catalogs,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'Catalogs', 'url' => null],
        ];

        $nav_bar = 'catalogs';
        $pageTitle = 'Catalogs';

        return view('admin.catalogs.list', compact('catalogs', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    public function add()
    {
        $pageTitle = "Add Catalog";
        $nav_bar = "catalogs";
        $videoTypes = VideoType::all();
        $categories = Category::all();
        $catalogs   = Catalog::all();
        $breadcrumbs = [
            ['label' => 'Catalogs', 'url' => route('catalog.index')],
            ['label' => 'Add Catalog', 'url' => null],
        ];
        $tags = Tag::all();

        return view('admin.catalogs.form', [
            'action' => 'Add',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [],
            'videoTypes' => $videoTypes,
            'categories' => $categories,
            'catalogs' => $catalogs,
            'tags' => $tags
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
            'status' => 'required|integer|in:0,1',
            'parent_catalog_id' => 'nullable|integer|exists:catalogs,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'is_promotional' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'video_type_id' => 'required|integer',
        ]);

        $catalog = Catalog::create([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags_text,
            'min_record_time' => $request->min_record_time,
            'max_record_time' => $request->max_record_time,
            'emoji' => $request->emoji,
            'status' => $request->status,
            'parent_catalog_id' => $request->parent_catalog_id,
            'category_id' => $request->category_id,
            'is_promotional' => $request->is_promotional,
            'is_premium' => $request->is_premium,
            'video_type_id' => $request->video_type_id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog created successfully.',
                'data' => $catalog->load(['category']),
            ], 201);
        }

        return redirect()->route('catalog.index')->with('success', 'Catalog created successfully.');
    }

    public function edit($id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return redirect()->route('catalog.index')->with('error', 'Catalog not found.');
        }

        $pageTitle = "Edit Catalog";
        $nav_bar = "catalogs";

        $videoTypes = VideoType::all();
        $categories = Category::all();

        $catalogs = Catalog::with(['videoType', 'category', 'parentCatalog'])->get();
        $breadcrumbs = [
            ['label' => 'Catalogs', 'url' => route('catalog.index')],
            ['label' => 'Edit Catalog', 'url' => null],
        ];
        $tags = Tag::all();

        return view('admin.catalogs.form', [
            'action' => 'Edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [$catalog],
            'admin.catalog.edit', compact('catalog', 'videoTypes', 'categories', 'catalogs'),
            'videoTypes' => $videoTypes,
            'categories' => $categories,
            'catalogs' => $catalogs,
            'tags' => $tags
        ]);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'tags_text' => 'nullable|string|max:255',
            'min_record_time' => 'required|integer|min:1',
            'max_record_time' => 'required|integer|max:30',
            'emoji' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1',
            'parent_catalog_id' => 'nullable|integer|exists:catalogs,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'is_promotional' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'video_type_id' => 'required|integer',
        ]);

        $catalog = Catalog::findOrFail($id);

        $catalog->update([
            'title' => $request->title,
            'description' => $request->description,
            'min_record_time' => $request->min_record_time,
            'max_record_time' => $request->max_record_time,
            'emoji' => $request->emoji,
            'status' => $request->status,
            'parent_catalog_id' => $request->parent_catalog_id,
            'category_id' => $request->category_id,
            'is_promotional' => $request->is_promotional ?? 0,
            'is_premium' => $request->is_premium ?? 0,
            'video_type_id' => $request->video_type_id,
            'tags' => $request->tags_text,
        ]);

        return redirect()->route('catalog.index')->with('success', 'Catalog updated successfully.');
    }


    public function activate($id)
    {
        $catalog = Catalog::findOrFail($id);
        $catalog->status = 1;
        $catalog->save();

        return redirect()->route('catalog.index')->with('success', 'Catalog activated successfully.');
    }

    public function deactivate($id)
    {
        $catalog = Catalog::findOrFail($id);
        $catalog->status = 0;
        $catalog->save();

        return redirect()->route('catalog.index')->with('success', 'Catalog deactivated successfully.');
    }

    public function destroy($id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return redirect()->route('catalog.index')->with('error', 'Catalog not found.');
        }

        $catalog->delete();

        return redirect()->route('catalog.index')->with('success', 'Catalog deleted successfully.');
    }

    public function getSuggestedCatalogs($userId = null, $limit = 3)
    {
        // Find all active catalogs in the current category
        $categoryId = $this->catalog->category_id ?? 0;
        $today = now()->toDateString();
        $allCatalogs = Catalog::where('is_deleted', 0)
            ->where('status', 1)
            ->where('category_id', $categoryId)
            ->get(['id', 'title', 'description', 'emoji', 'video_type_id', 'category_id']);

        // Get catalogs skipped by the user today
        $skippedCatalogIds = SkipVijo::where('user_id', $userId)
            ->whereDate('skipped_at', $today)
            ->pluck('catalog_id')
            ->toArray();

        // Count how many times each catalog was recorded by the user today
        $catalogCounts = VideoRequest::where('user_id', $userId)
            ->whereIn('catalog_id', $allCatalogs->pluck('id'))
            ->whereDate('created_at', $today)
            ->selectRaw('catalog_id, COUNT(*) as count')
            ->groupBy('catalog_id')
            ->pluck('count', 'catalog_id');

        // Find the lowest number of recordings
        $minCount = $catalogCounts->count() ? $catalogCounts->min() : 0;

        // Filter catalogs that the user recorded the least number of times and did not skip today
        $suggestedCatalogs = $allCatalogs->filter(function($catalog) use ($catalogCounts, $minCount, $skippedCatalogIds) {
            return ($catalogCounts[$catalog->id] ?? 0) == $minCount && !in_array($catalog->id, $skippedCatalogIds);
        });

        // Avoid recommending the same catalog that is currently being displayed
        $suggestedCatalogs = $suggestedCatalogs->filter(function($catalog) {
            return $catalog->id !== ($this->catalog->id ?? 0);
        });

        // If there are not enough, fill with other catalogs from other categories not skipped today
        if ($suggestedCatalogs->count() < $limit) {
            $needed = $limit - $suggestedCatalogs->count();
            $otherCatalogs = Catalog::where('is_deleted', 0)
                ->where('status', 1)
                ->where('category_id', '<>', $categoryId)
                ->whereNotIn('id', $skippedCatalogIds)
                ->where('id', '<>', ($this->catalog->id ?? 0))
                ->limit($needed)
                ->get(['id', 'title', 'description', 'emoji', 'video_type_id', 'category_id']);
            $suggestedCatalogs = $suggestedCatalogs->concat($otherCatalogs);
        }

        return $suggestedCatalogs->take($limit)->values();
    }

}
