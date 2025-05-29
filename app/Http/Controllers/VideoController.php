<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    // Listar vídeos com paginação, busca e filtro por request_id
    public function index(Request $request)
    {
        $query = Video::query();

        if ($request->has('request_id')) {
            $query->where('request_id', $request->input('request_id'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('video_name', 'like', "%$search%")
                  ->orWhere('thumbnail_name', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $videos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Videos retrieved successfully.',
            'data' => $videos,
        ]);
    }

    // Visualizar um vídeo específico
    public function show($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found.',
                'data' => null,
            ], 404);
        }

        if ($video->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this video.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Video retrieved successfully.',
            'data' => $video,
        ]);
    }

    // Criar um novo vídeo
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id'     => 'required|exists:video_requests,id',
            'video_name'     => 'required|string|max:255',
            'video_url'      => 'required|string',
            'video_duration' => 'required|integer',
            'thumbnail_name' => 'required|string|max:255',
            'thumbnail_url'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();
        $video = Video::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Video created successfully.',
            'data' => $video,
        ], 201);
    }

    // Atualizar um vídeo existente
    public function update(Request $request, $id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
            'success' => false,
            'message' => 'Video not found.',
            'data' => null,
            ], 404);
        }

        if ($video->user_id !== Auth::id()) {
            return response()->json([
            'success' => false,
            'message' => 'Unauthorized access to this video.',
            'data' => null,
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'request_id'     => 'sometimes|required|exists:video_requests,id',
            'video_name'     => 'sometimes|required|string|max:255',
            'video_url'      => 'sometimes|required|string',
            'video_duration' => 'sometimes|required|integer',
            'thumbnail_name' => 'sometimes|required|string|max:255',
            'thumbnail_url'  => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $video->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully.',
            'data' => $video,
        ]);
    }

    // Deletar um vídeo
    public function destroy($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found.',
                'data' => null,
            ], 404);
        }

        if ($video->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this video.',
                'data' => null,
            ], 403);
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully.',
            'data' => ['id' => $id],
        ]);
    }

    // Upload e salvar vídeo
    public function uploadAndStore(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:video_requests,id',
            'file' => 'required|file|mimes:mp4,mov,ogg,qt,webm,mkv|max:512000',
            'video_duration' => 'nullable|integer',
        ]);

        // Chama o método do MediaStorageController
        $mediaController = app(\App\Http\Controllers\MediaStorageController::class);
        $uploadResponse = $mediaController->uploadVideo($request);

        $uploadData = $uploadResponse->getData(true);

        if (empty($uploadData['success']) || !$uploadData['success']) {
            return response()->json([
                'success' => false,
                'message' => $uploadData['message'] ?? 'Upload failed.',
                'errors'  => $uploadData['errors'] ?? null,
            ], 422);
        }

        $video = Video::create([
            'request_id'     => $request->input('request_id'),
            'video_name'     => $uploadData['video_name'],
            'video_url'      => $uploadData['video_url'],
            'video_duration' => $uploadData['video_duration'],
            'thumbnail_name' => $uploadData['thumbnail_name'],
            'thumbnail_url'  => $uploadData['thumbnail_url'],
            'user_id'        => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded and saved successfully.',
            'data'    => $video,
        ], 201);
    }
}