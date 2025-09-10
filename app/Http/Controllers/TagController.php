<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Category;


class TagController extends Controller
{
    public function index()
    {
    if (request()->wantsJson()) {
        $tags = Tag::with(['category', 'creator'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Tags retrieved successfully.',
            'data' => $tags,
        ]);
    }

    $tags = Tag::with(['category', 'creator'])->get();

    $breadcrumbs = [
        ['label' => 'Tags', 'url' => null],
    ];

    $nav_bar = 'tags';
    $pageTitle = 'Tags';

    return view('admin.tags.list', compact('tags', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    public function add()
    {
        Log::info('TagController@create chamado');
        $pageTitle = "Add Tag";
        $nav_bar = "tags";

          // Pega todos os usuários e categorias
        $users = User::all();
        $selectedUserId = old('created_by_user', $info[0]->created_by_user ?? null);
        $categories = Category::all();

        // Pega os valores do enum 'type' da tabela tags
        $typeColumn = \DB::select("SHOW COLUMNS FROM tags LIKE 'type'");
        $types = [];

        if (!empty($typeColumn)) {
            // $typeColumn[0]->Type contém algo como: enum('catalog','journalTag','custom')
            $types = explode("','", preg_replace("/^enum\('(.*)'\)$/", "$1", $typeColumn[0]->Type));
        }

        $breadcrumbs = [
            ['label' => 'tags', 'url' => route('tag.index')],
            ['label' => 'Add Tags', 'url' => null],
        ];
          

        return view('admin.tags.form', [
            'action' => 'Add',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [],
            'users' => $users,
            'categories' => $categories,
            'types' => $types,
            'selectedUserId' => old('created_by_user', null),
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
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:catalog,journalTag,custom',
            'created_by_user' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $tag = Tag::create($request->all());

        // Redireciona para a lista de tags
        return redirect()->route('tag.index')
        ->with('success', 'Tag created successfully.');
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
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:catalog,journalTag,custom',
            'created_by_user' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $tag->update($request->all());
         // Redireciona para a lista de tags
        return redirect()->route('tag.index')
        ->with('success', 'Tag update successfully.');
    }

    public function destroy($id){
    Log::info('TagController@destroy chamado', ['id' => $id]);

    $tag = Tag::find($id);

    if (!$tag) {
        Log::warning('Tag não encontrado para deletar', ['id' => $id]);
        return redirect()->route('tag.index')->with('error', 'Catalog not found.');
    }

    $tag->delete();

    Log::info('Tag deletado', ['id' => $id]);

    return redirect()->route('tag.index')->with('success', 'Tag deleted successfully.');
    }

    public static function handleProvidedTags($tags, $categoryId = null)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $userId = Auth::id() ?? 0;
        $tagIds = [];

        if (empty($tags) || !is_array($tags)) {
            return $tagIds; // Return empty array if no tags provided
        }

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

    public static function getUserTags($categoryId = 0, $userId = null, $withCustomTags = true)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $catalogTags = Tag::where('type', 'journalTag')
            ->where('category_id', $categoryId)
            ->where('status', 1)
            ->get(['id', 'name'])
            ->toArray();

        if ($withCustomTags) {
            $customTags = Tag::where('type', 'custom')
                ->where('created_by_user', $userId)
                ->where('status', 1)
                ->get(['id', 'name'])
                ->toArray();

            $mergeTags = array_merge($catalogTags, $customTags);
            $catalogTags = array_map(function ($tag) {
                return [
                    'id' => (string)$tag['id'],
                    'name' => $tag['name'],
                ];
            }, $mergeTags);
        }

        return $catalogTags;
    }

    public function edit($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return redirect()->route('tag.index')->with('error', 'Tag not found.');
        }

        $pageTitle = "Edit Tag";
        $nav_bar = "tags";

        $users = User::all();
        $categories = Category::all();
        $typeColumn = \DB::select("SHOW COLUMNS FROM tags LIKE 'type'");
        $types = [];

        if (!empty($typeColumn)) {
            $types = explode("','", preg_replace("/^enum\('(.*)'\)$/", "$1", $typeColumn[0]->Type));
        }

        $breadcrumbs = [
            ['label' => 'Tags', 'url' => route('tag.index')],
            ['label' => 'Edit Tag', 'url' => null],
        ];

        $selectedUserId = old('created_by_user', $tag->created_by_user);

        return view('admin.tags.form', [
            'action' => 'Edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'info' => [$tag],
            'users' => $users,
            'categories' => $categories,
            'types' => $types,
            'selectedUserId' => $selectedUserId,
        ]);
    }


    public function deactivate($id){
        $tag = Tag::find($id);

        if (!$tag) {
            return redirect()->route('tag.index')->with('error', 'Tag not found.');
        }

        $tag->status = 0; // 0 = desativada
        $tag->save();

        return redirect()->route('tag.index')->with('success', 'Tag deactivated successfully.');
    }

    public function activate($id){
        $tag = Tag::find($id);

        if (!$tag) {
            return redirect()->route('tag.index')->with('error', 'Tag not found.');
        }

        $tag->status = 1; // 1 = ativada
        $tag->save();

        return redirect()->route('tag.index')->with('success', 'Tag activated successfully.');
    }
}
