<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Models\VideoRequest;
use App\Services\ApiService;
use App\Services\Emlo\EmloHelperService;
use App\Services\TranscriptionService;
use App\Services\VideoProcessingPipeline;

use App\Services\Emlo\Aggregation\PostRequestAggregation;

use App\Attributes\ConsumesUserCredits;

#[ConsumesUserCredits(cost: 5, feature: 'video_processing')]
class ProcessVideoRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The video request instance.
     *
     * @var \App\Models\VideoRequest
     */
    protected $videoRequest;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\VideoRequest  $videoRequest
     * @return void
     */
    public function __construct(VideoRequest $videoRequest)
    {
        $this->videoRequest = $videoRequest;
        Log::info('ProcessVideoRequest job constructed', ['video_request_id' => $this->videoRequest->id]);
    }

    /**
     * Execute the job.
     * @param  ApiService  $apiService
     * @param  EmloHelperService  $emloHelperService
     * @param  TranscriptionService  $transcriptionService
     * @param PostRequestAggregation $postRequestAggregation
     * @return void
     */
    public function handle(
        ApiService $apiService,
        EmloHelperService $emloHelperService,
        TranscriptionService $transcriptionService,
        PostRequestAggregation $postRequestAggregation

    ) {
        $context = [
            'videoRequest' => $this->videoRequest,
            'apiService' => $apiService,~
            'emloHelperService' => $emloHelperService,
            'transcriptionService' => $transcriptionService,
            'postRequestAggregation' => $postRequestAggregation
        ];

        $pipeline = new VideoProcessingPipeline();
        $pipeline->process($context);
    }
}