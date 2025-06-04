<?php

namespace App\Http\Controllers;

use App\Models\CatalogAnswer;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogAnswerController extends Controller
{
    public function index()
    {
        $answers = CatalogAnswer::with(['user', 'catalog', 'request'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Catalog answers retrieved successfully.',
            'data' => $answers,
        ]);
    }

    public function show($id)
    {
        $answer = CatalogAnswer::with(['user', 'catalog', 'request'])->find($id);
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog answer not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Catalog answer retrieved successfully.',
            'data' => $answer,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer|exists:video_requests,id',
            'catalog_id' => 'required|integer|exists:catalogs,id',
            'cred_score' => 'nullable|numeric',
            'metric1_answer' => 'nullable|string|max:50',
            'metric1Range' => 'nullable|numeric',
            'metric1Significance' => 'nullable|integer',
            'metric2_answer' => 'nullable|string|max:50',
            'metric2Range' => 'nullable|numeric',
            'metric2Significance' => 'nullable|integer',
            'metric3_answer' => 'nullable|string|max:50',
            'metric3Range' => 'nullable|numeric',
            'metric3Significance' => 'nullable|integer',
            'n8n_executionId' => 'nullable|string|max:50',
            'video_thumbnail_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fields = [
            'user_id',
            'catalog_id',
            'request_id',
            'cred_score',
            'metric1_answer',
            'metric1Range',
            'metric1Significance',
            'metric2_answer',
            'metric2Range',
            'metric2Significance',
            'metric3_answer',
            'metric3Range',
            'metric3Significance',
            'n8n_executionId',
        ];

        $data = $request->only($fields);
        $data['user_id'] = Auth::id();
        $answer = CatalogAnswer::create($data);

        if ($request->hasFile('video_thumbnail_file')) {
            $file = $request->file('video_thumbnail_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $disk = env('FILESYSTEM_DISK', 's3');
            $thumbnailPath = 'thumbnails/' . $fileName;
            Storage::disk($disk)->putFileAs('thumbnails', $file, $fileName);

            $thumbnailUrl = Storage::disk($disk)->url($thumbnailPath);

            Video::create([
                'request_id'     => $request->input('request_id'),
                'thumbnail_name' => $fileName,
                'thumbnail_url'  => $thumbnailUrl,
                'user_id'        => Auth::id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Catalog answer created successfully.',
            'data' => [
                'request_id' => $answer->request_id,
                'record_category' => 0,
                'record_date' => $request->input('record_date', now()),
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $answer = CatalogAnswer::find($id);
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog answer not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'catalog_id' => 'sometimes|required|integer|exists:catalogs,id',
            'request_id' => 'sometimes|required|integer|exists:video_requests,id',
            'cred_score' => 'nullable|numeric',
            'metric1_answer' => 'nullable|string|max:50',
            'metric1Range' => 'nullable|numeric',
            'metric1Significance' => 'nullable|integer',
            'metric2_answer' => 'nullable|string|max:50',
            'metric2Range' => 'nullable|numeric',
            'metric2Significance' => 'nullable|integer',
            'metric3_answer' => 'nullable|string|max:50',
            'metric3Range' => 'nullable|numeric',
            'metric3Significance' => 'nullable|integer',
            'n8n_executionId' => 'nullable|string|max:50',
        ]);

        $answer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Catalog answer updated successfully.',
            'data' => $answer->load(['user', 'catalog', 'request']),
        ]);
    }

    public function destroy($id)
    {
        $answer = CatalogAnswer::find($id);
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog answer not found.',
                'data' => null,
            ], 404);
        }
        $answer->delete();
        return response()->json([
            'success' => true,
            'message' => 'Catalog answer deleted successfully.',
            'data' => null,
        ]);
    }
}