<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Http\Controllers\VideoController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

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
        Log::info('Processing video upload', [
            'videoRequestId' => $this->videoRequestId,
            'filePath' => $this->filePath,
            'videoDuration' => $this->videoDuration,
            'originalName' => $this->originalName,
        ]);

        try {
            // Verify file exists before processing
            if (!file_exists($this->filePath)) {
                Log::error('Video file not found: ' . $this->filePath);
                return;
            }

            $request = new Request([
                'request_id' => $this->videoRequestId,
                'video_duration' => $this->videoDuration,
                'file_path' => $this->filePath,
            ]);

            $request->files->set('file', new UploadedFile(
                $this->filePath,
                $this->originalName,
                null,
                null,
                true
            ));

            $videoController = app(VideoController::class);
            $videoController->uploadAndStore($request);

            Log::info('Video upload processed successfully for videoRequestId: ' . $this->videoRequestId);
        } catch (\Exception $e) {
            Log::error('Error processing video upload: ' . $e->getMessage(), [
                'videoRequestId' => $this->videoRequestId,
                'filePath' => $this->filePath,
                'exception' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to mark job as failed
        } finally {
            // Always clean up the temporary file
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
                Log::info('Cleaned up temporary file: ' . $this->filePath);
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('ProcessVideoUpload job failed', [
            'videoRequestId' => $this->videoRequestId,
            'filePath' => $this->filePath,
            'error' => $exception->getMessage(),
        ]);

        // Clean up file even on failure
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }
}
