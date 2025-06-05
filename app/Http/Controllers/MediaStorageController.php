<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\File;

class MediaStorageController extends Controller
{
    /**
     * Upload video to the configured storage (S3 or local).
     */
    public function uploadVideo(Request $request, $userId = null)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,ogg,qt,webm,mkv|max:512000', // up to 500MB
            'video_duration' => 'nullable|integer',
        ]);

        // Prepare variables
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();

        $tempFolderPath = 'app' . DIRECTORY_SEPARATOR . 'temp';

        $localVideoPath = $request->file_path;

        $thumbnailName = Str::of($fileName)->basename('.' . $fileExtension) . '.jpg';
        $thumbnailPath = storage_path($tempFolderPath . DIRECTORY_SEPARATOR . $thumbnailName);
        // Generate thumbnail
        shell_exec("ffmpeg -y -i " . escapeshellarg($localVideoPath) . " -frames:v 1 -update 1 " . escapeshellarg($thumbnailPath));

        $originalSize = filesize($localVideoPath);

        // Compression/conversion to MP4 if necessary
        if (strtolower($fileExtension) === 'mp4' && $originalSize < 30000000) {
            $outputFile = $localVideoPath;
        } else {
            $outputFile = str_replace("." . $fileExtension, ".mp4", $localVideoPath);
            $ffmpegCommand = "ffmpeg -i " . escapeshellarg($localVideoPath) . " -c:v libx264 -profile:v baseline -level 3.0 -pix_fmt yuv420p -vf 'scale=trunc(iw/2)*2:trunc(ih/2)*2' -r 30 -b:v 1000k -c:a aac -b:a 128k -movflags +faststart " . escapeshellarg($outputFile);
            shell_exec($ffmpegCommand);
        }

        // Get video duration BEFORE uploading/cleanup (while file still exists)
        $duration = $request->input('video_duration');
        if (empty($duration)) {
            $ffprobe = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($outputFile);
            $durationOutput = shell_exec($ffprobe);
            if ($durationOutput) {
                $duration = (int) floatval($durationOutput);
            }
        }

        // Upload to S3 or local
        $disk = env('FILESYSTEM_DISK', 'local');
        $videoStoragePath = 'videos/' . basename($outputFile);
        $thumbnailStoragePath = 'thumbnails/' . $thumbnailName;
        Log::info('Uploading video to disk: ' . $disk);
        $videoUrl = $thumbnailUrl = '';
        if (file_exists($outputFile)) {
            Storage::disk($disk)->putFileAs('videos', new File($outputFile), basename($outputFile));
            $videoUrl = Storage::disk($disk)->url($videoStoragePath);
        }
        if (file_exists($thumbnailPath)) {
            Storage::disk($disk)->putFileAs('thumbnails', new File($thumbnailPath), $thumbnailName);
            $thumbnailUrl = Storage::disk($disk)->url($thumbnailStoragePath);
        }

        // Clean up local temporary files (ONLY ONCE, AT THE END)
        Log::info('Cleaning up temporary files: ' . $localVideoPath . ', ' . $thumbnailPath);
        @unlink($localVideoPath);
        @unlink($thumbnailPath);
        // Only delete $outputFile if it's different from $localVideoPath
        if ($outputFile !== $localVideoPath) {
            @unlink($outputFile);
        }

        return response()->json([
            'success'         => true,
            'message'         => 'Video uploaded successfully',
            'video_name'      => basename($outputFile),
            'video_url'       => $videoUrl,
            'video_duration'  => $duration,
            'thumbnail_name'  => $thumbnailName,
            'thumbnail_url'   => $thumbnailUrl,
        ]);
    }

    /**
     * Upload csv to the configured storage (S3 or local).
     */
    public function uploadCsv(Request $request)
    {
        // Process based on which input was provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $tempFolderPath = 'app' . DIRECTORY_SEPARATOR . 'temp';

            $localCsvPath = storage_path($tempFolderPath . DIRECTORY_SEPARATOR . $fileName);
            $file->move(storage_path($tempFolderPath), $fileName);
            // Handle uploaded file
        } elseif ($request->has('csv_path')) {
            $localCsvPath = $request->csv_path;
        }

        // Upload to S3 or local
        $disk = env('FILESYSTEM_DISK', 'local');
        $csvStoragePath = 'csvs/' . basename($localCsvPath);
        Log::info('Uploading csv to disk: ' . $disk);
        $csvUrl = '';
        if (file_exists($localCsvPath)) {
            Storage::disk($disk)->putFileAs('csvs', new File($localCsvPath), basename($localCsvPath));
            $csvUrl = Storage::disk($disk)->url($csvStoragePath);
        }

        // Clean up local temporary files (ONLY ONCE, AT THE END)
        Log::info('Cleaning up temporary files: ' . $localCsvPath);
        @unlink($localCsvPath);

        return response()->json([
            'success'         => true,
            'message'         => 'Csv uploaded successfully',
            'csv_name'      => basename($localCsvPath),
            's3_url' => $csvUrl
        ]);
    }

    /**
     * Streaming or download of video/image.
     */
    public function stream($type, $filename)
    {
        $disk = config('filesystems.default', 'local');
        $folder = $type === 'video' ? 'videos' : 'thumbnails';
        $path = $folder . '/' . $filename;

        if (!Storage::disk($disk)->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' not found.',
            ], 404);
        }

        $stream = Storage::disk($disk)->readStream($path);
        $mime = Storage::disk($disk)->mimeType($path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Remove video or image from storage.
     */
    public function delete($type, $filename)
    {
        $disk = config('filesystems.default', 'local');
        $folder = $type === 'video' ? 'videos' : 'thumbnails';
        $path = $folder . '/' . $filename;

        if (!Storage::disk($disk)->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' not found.',
            ], 404);
        }

        Storage::disk($disk)->delete($path);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' deleted successfully.',
        ]);
    }
}
