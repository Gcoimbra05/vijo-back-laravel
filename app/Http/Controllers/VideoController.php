<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoRequest;
use App\Services\Emlo\EmloResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
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

    public function uploadAndStore(Request $request)
    {
        $startTime = microtime(true);
        
        Log::info('VideoController: Starting uploadAndStore', [
            'request_id' => $request->input('request_id')
        ]);

        try {
            // Step 1: Validation
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|exists:video_requests,id',
                'file' => 'required|file|mimes:mp4,mov,ogg,qt,webm,mkv|max:512000',
                'video_duration' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                Log::error('VideoController: Validation failed', [
                    'errors' => $validator->errors()->all()
                ]);
                throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
            }

            $videoRequestId = $request->input('request_id');

            // Step 2: Verify VideoRequest exists
            $videoRequest = VideoRequest::find($videoRequestId);
            if (!$videoRequest) {
                throw new \Exception('VideoRequest not found: ' . $videoRequestId);
            }

            // Step 3: Upload and process video
            $mediaController = app(MediaStorageController::class);
            $uploadResponse = $mediaController->uploadVideo($request);
            $uploadData = $uploadResponse->getData(true);

            Log::info('VideoController: Upload response received', [
                'success' => $uploadData['success'] ?? false
            ]);

            if (empty($uploadData['success']) || !$uploadData['success']) {
                $errorMsg = $uploadData['message'] ?? 'Upload failed';
                $errors = $uploadData['errors'] ?? [];
                
                Log::error('VideoController: Upload failed', [
                    'message' => $errorMsg,
                    'errors' => $errors
                ]);
                
                throw new \Exception('Upload failed: ' . $errorMsg);
            }

            // Step 4: Save video to database with transaction
            DB::beginTransaction();

            try {
                Log::info('VideoController: Saving video to database', [
                    'videoRequestId' => $videoRequestId,
                    'videoName' => $uploadData['video_name']
                ]);

                $video = Video::where('request_id', $videoRequestId)
                    ->whereNull('video_url')
                    ->whereNotNull('thumbnail_url')
                    ->first();

                $videoPath = config('app.url') . '/videos/' . $uploadData['video_name'];
                $thumbnailPath = config('app.url') . '/thumbnails/' . ($uploadData['thumbnail_name'] ?? '');

                if ($video) {
                    $video->update([
                        'video_name'     => $uploadData['video_name'],
                        'video_url'      => $videoPath,
                        'video_duration' => $uploadData['video_duration'] ?? 0,
                    ]);
                    
                    Log::info('VideoController: Video updated in database');
                } else {
                    $video = Video::create([
                        'request_id'     => $videoRequestId,
                        'video_name'     => $uploadData['video_name'],
                        'video_url'      => $videoPath,
                        'video_duration' => $uploadData['video_duration'] ?? 0,
                        'thumbnail_name' => $uploadData['thumbnail_name'] ?? '',
                        'thumbnail_url'  => $thumbnailPath,
                        'user_id'        => Auth::id(),
                    ]);
                    
                    Log::info('VideoController: Video created in database');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('VideoController: Database operation failed', [
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Failed to save video to database: ' . $e->getMessage());
            }

            // Step 5: Initialize video processing
            try {
                Log::info('VideoController: Initializing video processing');
                
                $videoRequestController = app(VideoRequestController::class);
                $videoRequestController->initProcess($request, $videoRequestId);
                
                Log::info('VideoController: Processing initialized successfully');
                
            } catch (\Exception $e) {
                // Log but don't fail - processing can be retried
                Log::error('VideoController: Failed to initialize processing', [
                    'error' => $e->getMessage(),
                    'videoRequestId' => $videoRequestId
                ]);
            }

            $duration = round(microtime(true) - $startTime, 2);
            Log::info('VideoController: uploadAndStore completed', [
                'videoRequestId' => $videoRequestId,
                'duration' => $duration . 's'
            ]);

        } catch (\Exception $e) {
            Log::error('VideoController: uploadAndStore failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Re-throw to be handled by job
            throw $e;
        }
    }
}