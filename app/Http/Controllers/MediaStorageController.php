<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\File as FacadeFile;
use Intervention\Image\Facades\Image;

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

    public static function uploadThumbnail(Request $request)
    {
        $request->validate([
            'video_thumbnail_file' => 'required',
        ]);
        $image = $request->file('video_thumbnail_file');
        $maxSize = 500 * 1024; // 500kb em bytes
        $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();

        $tmpPath = $image->storeAs('temp', $fileName, 'local');
        $fullPath = storage_path('app/' . $tmpPath);
        $fileSize = filesize($fullPath);

        // Se a imagem for maior que 500kb, comprime proporcionalmente
        if ($fileSize > $maxSize) {
            $img = \Image::make($fullPath);

            // Calcula a qualidade proporcional ao excesso
            // Exemplo: 600kb = 92, 1mb = 80, 2mb = 60, 5mb = 30
            $excessRatio = min(1, ($fileSize - $maxSize) / (5 * 1024 * 1024 - $maxSize));
            $quality = 92 - intval($excessRatio * 62); // 92 até 30

            // Garante limites de qualidade
            $quality = max(30, min(92, $quality));

            $img->save($fullPath, $quality, $image->extension() === 'png' ? 'png' : 'jpg');
        }

        $disk = env('FILESYSTEM_DISK', 's3');
        $thumbnailPath = 'thumbnails/' . $fileName;
        Storage::disk($disk)->putFileAs('thumbnails', $file, $fileName);

        $thumbnailUrl = Storage::disk($disk)->url($thumbnailPath);

        return [
            'success'         => true,
            'thumbnail_name'  => $fileName,
            'thumbnail_url'   => $thumbnailUrl,
        ];
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

    public static function handlePublicFiles($type, $filename)
    {
        Log::info('Handling public file request for type: ' . $type . ', filename: ' . $filename);
            /* // Check if the file belongs to the authenticated user
            if (!self::fileBelongsToUser($type, $filename)) {
                abort(403, 'Unauthorized access to the file.');
            } */

        $path = $type . "/" . $filename;
        if (env('FILESYSTEM_DISK') === 's3') {
            // Check if the file exists on S3
            if (!Storage::disk('s3')->exists($path)) {
                Log::info('File not found on S3: ' . $path);
                abort(404);
            }

            // Get the file URL on S3
            $url = Storage::disk('s3')->url($path);
            $mimeType = Storage::disk('s3')->mimeType($path);
            $size = Storage::disk('s3')->size($path);

            // Set HTTP headers
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
                'Content-Length' => $size,
                'Accept-Ranges' => 'bytes', // Add support for byte ranges
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT', // Expiration date
                'Access-Control-Allow-Origin' => '*',
            ];

            // Return the response with the file
            return response()->stream(function () use ($path) {
                $stream = Storage::disk('s3')->getDriver()->readStream($path);
                fpassthru($stream);
            }, 200, $headers);
        } else {
            if (!FacadeFile::exists($path)) {
                abort(404);
            }

            $mimeType = FacadeFile::mimeType($path);
            if (empty($mimeType) && $type == 'video') {
                if (strpos($filename, '.mp4') !== false) {
                    $mimeType = 'video/mp4';
                } else if (strpos($filename, '.webm') !== false) {
                    $mimeType = 'video/webm';
                }
            }

            // Set HTTP headers
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                'Content-Length' => filesize($path),
                'Accept-Ranges' => 'bytes', // Add support for byte ranges
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT', // Expiration date
                'Access-Control-Allow-Origin' => '*',
            ];

            // Return the response with the file
            return response()->file($path, $headers);
        }

        abort(404);
    }

    /* private static function fileBelongsToUser($type, $filename)
    {
        if (in_array($type, ['system_images', 'images', 'thumbnails'])) {
            return true;
        }

        $page = $_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'];
        $allowedPages = [
            'prospect/check-references',
            'reference'
        ];

        foreach ($allowedPages as $allowedPage) {
            if (strpos($page, $allowedPage) !== false) {

                return true; // provisório
                $parts = explode('/', $page);
                $code = end($parts);
                $requestType = $parts[3] ?? $parts[count($parts) - 2];

                if ($requestType == 'reference') {
                    $request = RequestReferenceDetail::where('reference_code', $code)->select('user_id')->first();
                } else {
                    $request = RequestProspect::where('prospect_code', $code)->select('user_id')->first();
                }
    
                if ($request) {
                    return self::checkFileOwnership($type, $filename, $request->user_id);
                }
            }
        }

        return self::checkFileOwnership($type, $filename);
    }

    private static function checkFileOwnership($type, $filename)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        if ($type == self::PROPERTIES[self::VIDEO]['path']) {
            $document = VideoDemo::where('name', $filename)->first();
            if ($document) {
                if ($document->is_public) {
                    return true;
                }
                return $document->user_id == $userId;
            }
        }

        if (!$userId) {
            return false;
        }

        if (in_array($type, [self::PROPERTIES[self::REPORT]['path'], self::PROPERTIES[self::DOCUMENT]['path']])) {
            $document = Document::where('name', $filename)->first();
            if ($document) {
                return $document->user_id == $userId;
            }
        }

        // Se não encontrar o arquivo, retorna true para permitir o acesso, pois quer dizer que é um arquivo público.
        return true;
    } */
}
