<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\Facades\Image;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Validator;

class MediaStorageController extends Controller
{
    private static $bucket = null;
    private static $s3Client = null;

    private static function getS3Client()
    {
        if (self::$s3Client === null) {
            self::$s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region'),
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
                'http' => [
                    'timeout' => 30,
                    'connect_timeout' => 5,
                ]
            ]);

            self::$bucket = config('filesystems.disks.s3.bucket');
        }

        return self::$s3Client;
    }

    public static function getPresignedUrl($type, $filename, $expiresIn = 60)
    {
        $cacheKey = "presigned_url_{$type}_{$filename}";

        $cachedData = Cache::get($cacheKey);
        if ($cachedData && isset($cachedData['expires_at']) && now() < $cachedData['expires_at']) {
            return [
                'success' => true,
                'url' => $cachedData['url'],
                'expires_at' => $cachedData['expires_at'],
                'cached' => true
            ];
        }

        try {
            $s3 = self::getS3Client();
            $path = $type . '/' . $filename;

            try {
                $s3->headObject([
                    'Bucket' => self::$bucket,
                    'Key' => $path,
                ]);
            } catch (\Aws\S3\Exception\S3Exception $e) {
                return [
                    'success' => false,
                    'message' => 'File not found in S3',
                    'error' => $e->getMessage()
                ];
            }
            $cmd = $s3->getCommand('GetObject', [
                'Bucket' => self::$bucket,
                'Key' => $path,
                'ResponseContentDisposition' => 'inline',
                'ResponseCacheControl' => 'public, max-age=3600'
            ]);

            $request = $s3->createPresignedRequest($cmd, "+{$expiresIn} minutes");
            $presignedUrl = (string) $request->getUri();

            $expiresAt = now()->addMinutes($expiresIn);

            $cacheMinutes = intval($expiresIn * 0.9);
            $urlData = [
                'url' => $presignedUrl,
                'expires_at' => $expiresAt,
                'generated_at' => now()
            ];

            Cache::put($cacheKey, $urlData, $cacheMinutes);

            return [
                'success' => true,
                'url' => $presignedUrl,
                'expires_at' => $expiresAt,
                'cached' => false
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error generating presigned URL',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload video to the configured storage (S3 or local).
     * Robust version with granular error handling and cleanup.
     */
    public function uploadVideo(Request $request, $userId = null)
    {
        $startTime = microtime(true);
        $tempFiles = [];
        
        Log::info('MediaStorage: Starting uploadVideo process', [
            'file_path' => $request->file_path ?? 'unknown'
        ]);

        try {
            // Step 1: Validation
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:mp4,mov,ogg,qt,webm,mkv|max:512000',
                'video_duration' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                Log::error('MediaStorage: Validation failed', [
                    'errors' => $validator->errors()->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->all(),
                ], 422);
            }

            // Step 2: Prepare variables and validate file
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $localVideoPath = $request->file_path;

            // Validate source file
            if (!file_exists($localVideoPath)) {
                throw new \Exception('Source video file not found: ' . $localVideoPath);
            }

            if (!is_readable($localVideoPath)) {
                throw new \Exception('Source video file is not readable: ' . $localVideoPath);
            }

            $originalSize = filesize($localVideoPath);
            if ($originalSize === 0) {
                throw new \Exception('Source video file is empty');
            }

            Log::info('MediaStorage: File validated', [
                'size' => $originalSize,
                'extension' => $fileExtension
            ]);

            $tempFolderPath = 'app' . DIRECTORY_SEPARATOR . 'temp';
            $thumbnailName = Str::of($fileName)->basename('.' . $fileExtension) . '.jpg';
            $thumbnailPath = storage_path($tempFolderPath . DIRECTORY_SEPARATOR . $thumbnailName);
            $tempFiles[] = $thumbnailPath;

            // Remove existing thumbnail if exists
            if (file_exists($thumbnailPath)) {
                @unlink($thumbnailPath);
            }

            // Step 3: Generate thumbnail with retry
            $thumbnailGenerated = $this->generateThumbnail($localVideoPath, $thumbnailPath);
            if (!$thumbnailGenerated) {
                Log::warning('MediaStorage: Thumbnail generation failed, continuing without thumbnail');
            }

            // Step 4: Video conversion (if needed)
            $needsConversion = !(strtolower($fileExtension) === 'mp4' && $originalSize < 30000000);
            $outputFile = $localVideoPath;
            
            if ($needsConversion) {
                Log::info('MediaStorage: Video needs conversion', [
                    'originalSize' => $originalSize,
                    'extension' => $fileExtension
                ]);
                
                $outputFile = str_replace('.' . $fileExtension, '_converted.mp4', $localVideoPath);
                $tempFiles[] = $outputFile;
                
                if (file_exists($outputFile)) {
                    @unlink($outputFile);
                }
                
                $conversionSuccess = $this->convertVideo($localVideoPath, $outputFile);
                
                if (!$conversionSuccess) {
                    throw new \Exception('Video conversion failed');
                }
                
                Log::info('MediaStorage: Video converted successfully');
            } else {
                Log::info('MediaStorage: Video does not need conversion');
            }

            // Step 5: Get video duration
            $duration = $request->input('video_duration');
            if (empty($duration)) {
                $duration = $this->getVideoDuration($outputFile);
                if (!$duration) {
                    Log::warning('MediaStorage: Could not determine video duration, using default');
                    $duration = 0;
                }
            }

            Log::info('MediaStorage: Video duration determined', ['duration' => $duration]);

            // Step 6: Replace original with converted file if needed
            if ($outputFile !== $localVideoPath && file_exists($outputFile)) {
                $fileSize = filesize($outputFile);
                if ($fileSize === 0) {
                    throw new \Exception('Converted file is empty');
                }
                
                @unlink($localVideoPath);
                rename($outputFile, $localVideoPath);
                $outputFile = $localVideoPath;
                
                Log::info('MediaStorage: Replaced original with converted file');
            }

            // Step 7: Upload to storage (S3 or local)
            $disk = config('filesystems.default', 's3');
            $videoStoragePath = 'videos/' . basename($outputFile);
            $thumbnailStoragePath = 'thumbnails/' . $thumbnailName;
            
            Log::info('MediaStorage: Uploading to storage', ['disk' => $disk]);
            
            $videoUrl = $thumbnailUrl = '';
            
            // Upload video
            if (!file_exists($outputFile)) {
                throw new \Exception('Output file does not exist before upload');
            }
            
            try {
                Storage::disk($disk)->putFileAs('videos', new File($outputFile), basename($outputFile));
                $videoUrl = Storage::disk($disk)->url($videoStoragePath);
                Log::info('MediaStorage: Video uploaded successfully');
            } catch (\Exception $e) {
                Log::error('MediaStorage: Video upload failed', ['error' => $e->getMessage()]);
                throw new \Exception('Failed to upload video to storage: ' . $e->getMessage());
            }
            
            // Upload thumbnail (optional)
            if (file_exists($thumbnailPath)) {
                try {
                    Storage::disk($disk)->putFileAs('thumbnails', new File($thumbnailPath), $thumbnailName);
                    $thumbnailUrl = Storage::disk($disk)->url($thumbnailStoragePath);
                    Log::info('MediaStorage: Thumbnail uploaded successfully');
                } catch (\Exception $e) {
                    Log::warning('MediaStorage: Thumbnail upload failed', ['error' => $e->getMessage()]);
                    // Don't throw - thumbnail is optional
                }
            }

            $duration = round(microtime(true) - $startTime, 2);
            Log::info('MediaStorage: Upload process completed', [
                'totalDuration' => $duration . 's',
                'videoSize' => filesize($outputFile)
            ]);

            return response()->json([
                'success'         => true,
                'message'         => 'Video uploaded successfully',
                'video_name'      => basename($outputFile),
                'video_url'       => $videoUrl,
                'video_duration'  => $request->input('video_duration') ?? $duration,
                'thumbnail_name'  => $thumbnailName,
                'thumbnail_url'   => $thumbnailUrl,
            ]);
            
        } catch (\Exception $e) {
            Log::error('MediaStorage: Upload process failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Video upload failed: ' . $e->getMessage(),
                'errors' => [$e->getMessage()],
            ], 500);
            
        } finally {
            // Always cleanup temporary files
            $this->cleanupTempFiles($tempFiles);
            if (file_exists($localVideoPath)) {
                @unlink($localVideoPath);
            }
        }
    }

    /**
     * Generate thumbnail from video with retry logic
     */
    protected function generateThumbnail(string $videoPath, string $thumbnailPath, int $maxRetries = 2): bool
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info('MediaStorage: Generating thumbnail', [
                    'attempt' => $attempt,
                    'videoPath' => $videoPath
                ]);

                $command = sprintf(
                    'ffmpeg -y -i %s -vf "thumbnail,scale=640:360" -frames:v 1 -update 1 %s 2>&1',
                    escapeshellarg($videoPath),
                    escapeshellarg($thumbnailPath)
                );

                $output = shell_exec($command);

                if (file_exists($thumbnailPath) && filesize($thumbnailPath) > 0) {
                    Log::info('MediaStorage: Thumbnail generated successfully');
                    return true;
                }

                Log::warning('MediaStorage: Thumbnail generation attempt failed', [
                    'attempt' => $attempt,
                    'output' => substr($output, -500)
                ]);

                sleep(1); // Wait before retry

            } catch (\Exception $e) {
                Log::error('MediaStorage: Thumbnail generation error', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return false;
    }

    /**
     * Convert video with optimized settings and error handling
     */
    protected function convertVideo(string $inputPath, string $outputPath, int $maxRetries = 2): bool
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info('MediaStorage: Converting video', [
                    'attempt' => $attempt,
                    'inputSize' => filesize($inputPath)
                ]);

                // Optimized FFmpeg command with error recovery
                $command = sprintf(
                    'ffmpeg -y -i %s -c:v libx264 -preset fast -profile:v baseline -level 3.0 ' .
                    '-pix_fmt yuv420p -vf "scale=trunc(iw/2)*2:trunc(ih/2)*2" -r 30 -b:v 1000k ' .
                    '-c:a aac -b:a 128k -movflags +faststart -max_muxing_queue_size 1024 %s 2>&1',
                    escapeshellarg($inputPath),
                    escapeshellarg($outputPath)
                );

                $output = shell_exec($command);

                // Validate output
                if (file_exists($outputPath)) {
                    $outputSize = filesize($outputPath);
                    
                    if ($outputSize === 0) {
                        @unlink($outputPath);
                        throw new \Exception('Converted file is empty');
                    }

                    Log::info('MediaStorage: Video converted successfully', [
                        'inputSize' => filesize($inputPath),
                        'outputSize' => $outputSize,
                        'compressionRatio' => round(($outputSize / filesize($inputPath)) * 100, 2) . '%'
                    ]);

                    return true;
                }

                Log::warning('MediaStorage: Conversion attempt failed', [
                    'attempt' => $attempt,
                    'output' => substr($output, -500)
                ]);

                sleep(2); // Wait before retry

            } catch (\Exception $e) {
                Log::error('MediaStorage: Video conversion error', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if (file_exists($outputPath)) {
                    @unlink($outputPath);
                }
            }
        }

        return false;
    }

    /**
     * Get video duration using ffprobe with retry
     */
    protected function getVideoDuration(string $videoPath, int $maxRetries = 2): ?int
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $command = sprintf(
                    'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>&1',
                    escapeshellarg($videoPath)
                );

                $output = shell_exec($command);

                if ($output && is_numeric(trim($output))) {
                    $duration = (int) floatval(trim($output));
                    Log::info('MediaStorage: Duration determined', ['duration' => $duration]);
                    return $duration;
                }

                Log::warning('MediaStorage: Duration probe attempt failed', [
                    'attempt' => $attempt,
                    'output' => $output
                ]);

                sleep(1);

            } catch (\Exception $e) {
                Log::error('MediaStorage: Duration probe error', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return null;
    }

    /**
     * Cleanup temporary files safely
     */
    protected function cleanupTempFiles(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                try {
                    @unlink($file);
                    Log::info('MediaStorage: Cleaned up temp file', ['file' => basename($file)]);
                } catch (\Exception $e) {
                    Log::warning('MediaStorage: Failed to cleanup temp file', [
                        'file' => basename($file),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
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

        $disk = config('filesystems.default', 's3');
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
        $disk = config('filesystems.default', 's3');
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

    public static function handlePublicFiles(Request $request, $type, $filename)
    {
        Log::info('Handling public file request for type: ' . $type . ', filename: ' . $filename);

        $path = $type . '/' . $filename;

        $bucket = config('filesystems.disks.s3.bucket');
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        try {
            $head = $s3->headObject([
                'Bucket' => $bucket,
                'Key'    => $path,
            ]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            abort(404, 'Arquivo não encontrado no S3');
        }

        $size = $head['ContentLength'];
        $mimeType = $head['ContentType'] ?? 'application/octet-stream';

        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            'Access-Control-Allow-Origin' => '*',
        ];

        $range = $request->header('Range');
        if ($range && preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
            $start = intval($matches[1]);
            $end = $matches[2] !== '' ? intval($matches[2]) : $size - 1;
            $length = $end - $start + 1;

            $headers['Content-Range'] = "bytes $start-$end/$size";
            $headers['Content-Length'] = $length;

            $object = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $path,
                'Range' => "bytes=$start-$end",
            ]);

            return response()->stream(function () use ($object) {
                $body = $object['Body'];
                while (!$body->eof()) {
                    echo $body->read(1024 * 1024); // 1MB chunks
                    flush();
                }
            }, 206, $headers); // Partial Content
        }

        // w/o range, return full content
        $headers['Content-Length'] = $size;

        $object = $s3->getObject([
            'Bucket' => $bucket,
            'Key' => $path,
        ]);

        return response()->stream(function () use ($object) {
            $body = $object['Body'];
            while (!$body->eof()) {
                echo $body->read(1024 * 1024);
                flush();
            }
        }, 200, $headers); // Full content
    }

    public static function streamWithPresignedUrl(Request $request, $type, $filename)
    {
        $urlResult = self::getPresignedUrl($type, $filename, 60);

        if (!$urlResult['success']) {
            return self::handlePublicFiles($request, $type, $filename);
        }

        $presignedUrl = $urlResult['url'];
        $range = $request->header('Range');

        if ($range) {
            return redirect($presignedUrl, 302, [
                'Cache-Control' => 'no-cache'
            ]);
        }

        return redirect($presignedUrl, 302, [
            'Cache-Control' => 'public, max-age=300'
        ]);
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
