<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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

    /**
     * Upload video to the configured storage (S3 or local).
     */
    public function uploadVideo(Request $request, $userId = null)
    {
        Log::info('Starting uploadVideo process');

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:mp4,mov,ogg,qt,webm,mkv|max:512000', // up to 500MB
            'video_duration' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed in uploadVideo', [
                'errors' => $validator->errors()->all(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        // Prepare variables
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();

        $tempFolderPath = 'app' . DIRECTORY_SEPARATOR . 'temp';

        $localVideoPath = $request->file_path;

        $thumbnailName = Str::of($fileName)->basename('.' . $fileExtension) . '.jpg';
        $thumbnailPath = storage_path($tempFolderPath . DIRECTORY_SEPARATOR . $thumbnailName);
        
        // Remove existing thumbnail if it exists to prevent FFmpeg prompt
        if (file_exists($thumbnailPath)) {
            @unlink($thumbnailPath);
            Log::info('Removed existing thumbnail: ' . $thumbnailPath);
        }
        
        // Generate thumbnail
        $thumbnailCommand = "ffmpeg -y -i " . escapeshellarg($localVideoPath) . " -frames:v 1 -update 1 " . escapeshellarg($thumbnailPath) . " 2>&1";
        $thumbnailResult = shell_exec($thumbnailCommand);
        Log::info('Thumbnail generation result: ' . $thumbnailResult);

        $originalSize = filesize($localVideoPath);

        // Compression/conversion to MP4 if necessary
        if (strtolower($fileExtension) === 'mp4' && $originalSize < 30000000) { // MB = 30
            $outputFile = $localVideoPath;
        } else {
            $outputFile = str_replace("." . $fileExtension, ".mp4", $localVideoPath);
            
            // Remove existing output file if it exists to prevent FFmpeg prompt
            if ($localVideoPath != $outputFile && file_exists($outputFile)) {
                @unlink($outputFile);
                Log::info('Removed existing output file: ' . $outputFile);
            }

            $ffmpegCommand = "ffmpeg -y -i " . escapeshellarg($localVideoPath) . " -c:v libx264 -profile:v baseline -level 3.0 -pix_fmt yuv420p -vf 'scale=trunc(iw/2)*2:trunc(ih/2)*2' -r 30 -b:v 1000k -c:a aac -b:a 128k -movflags +faststart " . escapeshellarg($outputFile) . " 2>&1";
            $conversionResult = shell_exec($ffmpegCommand);
            Log::info('Video conversion result: ' . $conversionResult);
        }

        // Get video duration BEFORE uploading/cleanup (while file still exists)
        $duration = $request->input('video_duration');
        if (empty($duration)) {
            $ffprobe = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($outputFile) . " 2>&1";
            $durationOutput = shell_exec($ffprobe);
            Log::info('FFprobe duration result: ' . $durationOutput);
            if ($durationOutput) {
                $duration = (int) floatval($durationOutput);
            }
        }

        // Upload to S3 or local
        $disk = config('filesystems.default', 's3');
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

    public static function handlePublicFilesNew(Request $request, $type, $filename)
    {
        $startTime = microtime(true);
        $path = $type . '/' . $filename;

        $s3 = self::getS3Client();
        $bucket = self::$bucket;

        Log::info('Iniciando requisição para arquivo: ' . $path);

        try {
            $head = $s3->headObject([
                'Bucket' => $bucket,
                'Key'    => $path,
            ]);

            Log::info('HeadObject concluído em: ' . (microtime(true) - $startTime) . 's');
        } catch (\Aws\S3\Exception\S3Exception $e) {
            Log::error('Erro HeadObject S3: ' . $e->getMessage());
            abort(404, 'Arquivo não encontrado no S3');
        }

        $size = $head['ContentLength'];
        $mimeType = $head['ContentType'] ?? 'video/mp4';

        $headers = [
            'Content-Type'        => $mimeType,
            'Accept-Ranges'       => 'bytes',
            'Cache-Control'       => 'public, max-age=31536000, immutable',
            'Expires'             => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Range',
            'Access-Control-Expose-Headers' => 'Content-Range, Content-Length, Accept-Ranges',
        ];

        $range = $request->header('Range');
        if ($range && preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
            $start = intval($matches[1]);
            $end = $matches[2] !== '' ? intval($matches[2]) : $size - 1;
            $end = min($end, $size - 1);
            $length = $end - $start + 1;

            $headers['Content-Range'] = "bytes $start-$end/$size";
            $headers['Content-Length'] = $length;

            Log::info("Range request: $start-$end ($length bytes)");

            try {
                $getObjectStart = microtime(true);
                $object = $s3->getObject([
                    'Bucket' => $bucket,
                    'Key'    => $path,
                    'Range'  => "bytes=$start-$end",
                ]);

                Log::info('GetObject range concluído em: ' . (microtime(true) - $getObjectStart) . 's');
                Log::info('Tempo total range: ' . (microtime(true) - $startTime) . 's');

                return response($object['Body']->getContents(), 206, $headers);

            } catch (\Aws\S3\Exception\S3Exception $e) {
                Log::error('Erro GetObject range S3: ' . $e->getMessage());
                abort(500, 'Erro ao carregar chunk do vídeo');
            }
        }

        // Sem range header - arquivo completo (raro para vídeos)
        $headers['Content-Length'] = $size;

        Log::info("Full content request: $size bytes");

        try {
            $getObjectStart = microtime(true);
            $object = $s3->getObject([
                'Bucket' => $bucket,
                'Key'    => $path,
            ]);

            Log::info('GetObject full concluído em: ' . (microtime(true) - $getObjectStart) . 's');
            Log::info('Tempo total full: ' . (microtime(true) - $startTime) . 's');

            return response($object['Body']->getContents(), 200, $headers);

        } catch (\Aws\S3\Exception\S3Exception $e) {
            Log::error('Erro GetObject full S3: ' . $e->getMessage());
            abort(500, 'Erro ao carregar arquivo');
        }
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
