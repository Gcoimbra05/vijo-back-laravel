<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\VideoType;
use App\Models\Category;

class CatalogController extends Controller
{
    protected $catalog;

    public function __construct(?Catalog $catalog = null)
    {
        Log::info('CatalogController::__construct chamado');
        $this->catalog = $catalog;
    }

    public function index(Request $request)
    {
          // Filtro por search (title)
        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        Log::info('CatalogController@index chamado');
        $catalogs = Catalog::orderBy('admin_order', 'asc')->get();
        Log::info('Catalogs encontrados', ['count' => $catalogs->count()]);

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
        Log::info('CatalogController@create chamado');
        $pageTitle = "Add Catalog";
        $nav_bar = "catalogs";
        $videoTypes = VideoType::all();
        $categories = Category::all();
        $catalogs   = Catalog::all();;
        $breadcrumbs = [
            ['label' => 'Catalogs', 'url' => route('catalog.index')],
            ['label' => 'Add Catalog', 'url' => null],
        ];

        return view('admin.catalogs.form', [
            'action' => 'Add',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [],
            'videoTypes' => $videoTypes,
            'categories' => $categories,
            'catalogs' => $catalogs,
        ]);

    }

    public function store(Request $request){
        Log::info('CatalogController@store chamado', ['request' => $request->all()]);

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
            'video_type_id' => 'required|integer',  // necessário
        ]);


    $catalog = Catalog::create($request->all());

    Log::info('Catalog criado', ['id' => $catalog->id]);

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
        Log::info('CatalogController@edit chamado', ['id' => $id]);
        $catalog = Catalog::find($id);

        if (!$catalog) {
            Log::warning('Catalog não encontrado para edição', ['id' => $id]);
            abort(404, 'Catalog not found.');
        }

        $pageTitle = "Edit Catalog";
        $nav_bar = "catalogs";

        $videoTypes = VideoType::all();
        $categories = Category::all();
        $catalogs   = Catalog::all();

        $catalog = Catalog::findOrFail($id);
        $catalogs = Catalog::with(['videoType', 'category', 'parentCatalog'])->get();
        $breadcrumbs = [
            ['label' => 'Catalogs', 'url' => route('catalog.index')],
            ['label' => 'Edit Catalog', 'url' => null],
        ];

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
        ]);

    }

    public function update(Request $request, $id){
    // Validação
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

    // Buscar catálogo
    $catalog = Catalog::findOrFail($id);

    // Atualizar com os dados do request
    $catalog->update($request->all());

    // Redirecionar com mensagem de sucesso
    return redirect()->route('catalog.index')->with('success', 'Catalog updated successfully.');
    }

    public function activate($id){
        $catalog = Catalog::findOrFail($id);
        $catalog->status = 1; // Ativo
        $catalog->save();

        return redirect()->route('catalog.index')->with('success', 'Catalog activated successfully.');
    }

    public function deactivate($id){
        $catalog = Catalog::findOrFail($id);
        $catalog->status = 0; // Inativo
        $catalog->save();

        return redirect()->route('catalog.index')->with('success', 'Catalog deactivated successfully.');
    }

    public function destroy($id){
    Log::info('CatalogController@destroy chamado', ['id' => $id]);

    $catalog = Catalog::find($id);

    if (!$catalog) {
        Log::warning('Catalog não encontrado para deletar', ['id' => $id]);
        return redirect()->route('catalog.index')->with('error', 'Catalog not found.');
    }

    $catalog->delete();

    Log::info('Catalog deletado', ['id' => $id]);

    return redirect()->route('catalog.index')->with('success', 'Catalog deleted successfully.');
    }

}
