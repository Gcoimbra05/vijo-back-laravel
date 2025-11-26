<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\SkipVijo;
use App\Models\User;
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

        // add emoji_rendered property so view shows the emoji (handles hex code, entity or char)
        foreach ($catalogs as $c) {
            $emojiVal = $c->emoji ?? '';
            if ($emojiVal) {
                $e = trim($emojiVal);
                if (preg_match('/^[0-9a-fA-F]{2,8}$/', $e)) {
                    $c->emoji_rendered = mb_convert_encoding('&#x' . $e . ';', 'UTF-8', 'HTML-ENTITIES');
                } elseif (preg_match('/^(&#x[0-9a-fA-F]+;|&#\d+;)/', $e)) {
                    $c->emoji_rendered = html_entity_decode($e, ENT_QUOTES, 'UTF-8');
                } else {
                    $c->emoji_rendered = $e;
                }
            } else {
                $c->emoji_rendered = '-';
            }
        }

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
            'from' => 'catalog',
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

        // Fixed: removed stray merge artifact and provide a clean array of data to the view
        return view('admin.catalogs.form', [
            'action' => 'Edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [$catalog],
            'videoTypes' => $videoTypes,
            'categories' => $categories,
            'catalogs' => $catalogs,
            'tags' => $tags,
            'from' => request('from', 'catalog'),
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
        $categoryId = $this->catalog->category_id ?? 0;

        $timezone = config('app.timezone', 'America/New_York');
        if ($userId) {
            $user = User::find($userId);
            if ($user && $user->timezone) {
                $timezone = $user->timezone;
            }
        }
        $today = now($timezone)->toDateString();

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
