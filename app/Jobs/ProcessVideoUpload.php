<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Http\Controllers\VideoController;
use App\Models\VideoRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes for large videos

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [60, 180, 300]; // 1min, 3min, 5min

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    protected $videoRequestId;
    protected $filePath;
    protected $videoDuration;
    protected $originalName;

    public function __construct($videoRequestId, $filePath, $videoDuration = null, $originalName = null)
    {
        $this->videoRequestId = $videoRequestId;
        $this->filePath = $filePath;
        $this->videoDuration = $videoDuration;
        $this->originalName = $originalName;
    }

    public function handle()
    {
        $startTime = microtime(true);
        
        Log::info('ProcessVideoUpload: Starting job', [
            'attempt' => $this->attempts(),
            'videoRequestId' => $this->videoRequestId,
            'filePath' => $this->filePath,
            'fileSize' => file_exists($this->filePath) ? filesize($this->filePath) : 0
        ]);

        // Step 1: Pre-flight validations
        if (!$this->validatePrerequisites()) {
            $this->cleanupTemporaryFiles();
            return; // Soft fail - don't retry
        }

        try {
            // Step 2: Process video upload with transaction
            DB::beginTransaction();
            
            $this->processVideoUpload();
            
            DB::commit();
            
            $duration = round(microtime(true) - $startTime, 2);
            Log::info('ProcessVideoUpload: Job completed successfully', [
                'videoRequestId' => $this->videoRequestId,
                'duration' => $duration . 's'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->handleProcessingError($e);
            
            // Re-throw only if we should retry
            if ($this->shouldRetry($e)) {
                throw $e;
            }
        } finally {
            // Always clean up temporary files
            $this->cleanupTemporaryFiles();
        }
    }

    /**
     * Validate all prerequisites before processing
     */
    protected function validatePrerequisites(): bool
    {
        // Check if video request exists
        $videoRequest = VideoRequest::find($this->videoRequestId);
        if (!$videoRequest) {
            Log::error('ProcessVideoUpload: VideoRequest not found', [
                'videoRequestId' => $this->videoRequestId
            ]);
            return false;
        }

        // Check if file exists
        if (!file_exists($this->filePath)) {
            Log::error('ProcessVideoUpload: Video file not found', [
                'videoRequestId' => $this->videoRequestId,
                'filePath' => $this->filePath
            ]);
            return false;
        }

        // Check if file is readable
        if (!is_readable($this->filePath)) {
            Log::error('ProcessVideoUpload: Video file is not readable', [
                'videoRequestId' => $this->videoRequestId,
                'filePath' => $this->filePath
            ]);
            return false;
        }

        // Check file size (must be > 0)
        $fileSize = filesize($this->filePath);
        if ($fileSize === 0) {
            Log::error('ProcessVideoUpload: Video file is empty', [
                'videoRequestId' => $this->videoRequestId,
                'filePath' => $this->filePath
            ]);
            return false;
        }

        Log::info('ProcessVideoUpload: Prerequisites validated successfully', [
            'videoRequestId' => $this->videoRequestId,
            'fileSize' => $fileSize
        ]);

        return true;
    }

    /**
     * Process the video upload
     */
    protected function processVideoUpload(): void
    {
        try {
            $request = new Request([
                'request_id' => $this->videoRequestId,
                'video_duration' => $this->videoDuration,
                'file_path' => $this->filePath,
            ]);

            $request->files->set('file', new UploadedFile(
                $this->filePath,
                $this->originalName ?? basename($this->filePath),
                null,
                null,
                true
            ));

            Log::info('ProcessVideoUpload: Calling VideoController', [
                'videoRequestId' => $this->videoRequestId
            ]);

            $videoController = app(VideoController::class);
            $videoController->uploadAndStore($request);

        } catch (\Exception $e) {
            Log::error('ProcessVideoUpload: Error in processVideoUpload', [
                'videoRequestId' => $this->videoRequestId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Handle processing errors
     */
    protected function handleProcessingError(\Exception $e): void
    {
        Log::error('ProcessVideoUpload: Job failed', [
            'attempt' => $this->attempts(),
            'videoRequestId' => $this->videoRequestId,
            'filePath' => $this->filePath,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Determine if we should retry based on exception type
     */
    protected function shouldRetry(\Exception $e): bool
    {
        // Don't retry on validation errors
        if (strpos($e->getMessage(), 'Validation') !== false) {
            Log::info('ProcessVideoUpload: Validation error, not retrying');
            return false;
        }

        // Don't retry if file is corrupted
        if (strpos($e->getMessage(), 'corrupted') !== false || 
            strpos($e->getMessage(), 'invalid') !== false) {
            Log::info('ProcessVideoUpload: File corrupted, not retrying');
            return false;
        }

        // Retry on other errors if we haven't exceeded max tries
        if ($this->attempts() < $this->tries) {
            Log::info('ProcessVideoUpload: Will retry', [
                'attempt' => $this->attempts(),
                'maxTries' => $this->tries
            ]);
            return true;
        }

        Log::info('ProcessVideoUpload: Max retries reached, not retrying');
        return false;
    }

    /**
     * Clean up temporary files
     */
    protected function cleanupTemporaryFiles(): void
    {
        if (file_exists($this->filePath)) {
            try {
                @unlink($this->filePath);
                Log::info('ProcessVideoUpload: Cleaned up temporary file', [
                    'filePath' => $this->filePath
                ]);
            } catch (\Exception $e) {
                Log::warning('ProcessVideoUpload: Failed to cleanup temporary file', [
                    'filePath' => $this->filePath,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle a job failure (called after all retries exhausted)
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('ProcessVideoUpload: Job permanently failed after all retries', [
            'videoRequestId' => $this->videoRequestId,
            'filePath' => $this->filePath,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Clean up file even on permanent failure
        $this->cleanupTemporaryFiles();

        // Optional: Update video request status to indicate failure
        try {
            $videoRequest = VideoRequest::find($this->videoRequestId);
            if ($videoRequest) {
                // You can add a status field or flag to indicate upload failure
                Log::info('ProcessVideoUpload: VideoRequest found, consider adding failure status', [
                    'videoRequestId' => $this->videoRequestId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ProcessVideoUpload: Error updating VideoRequest status', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['video-upload', 'video-request:' . $this->videoRequestId];
    }
}
