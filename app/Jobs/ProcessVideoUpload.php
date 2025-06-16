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

        @unlink($this->filePath);
    }
}
