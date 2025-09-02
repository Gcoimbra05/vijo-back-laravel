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
use App\Services\Emlo\EmloCsvService;
use App\Services\LlamaService;
use App\Services\VideoProcessingPipeline;

use App\Services\Emlo\Aggregation\PostRequestAggregation;

class ProcessVideoRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @param  EmloCsvService  $emloCsvService
     * @param  LlamaService $llamaService
     * @param PostRequestAggregation $postRequestAggregation
     * @return void
     */
    public function handle(
        ApiService $apiService,
        EmloHelperService $emloHelperService,
        TranscriptionService $transcriptionService,
        EmloCsvService $emloCsvService,
        LlamaService $llamaService,
        PostRequestAggregation $postRequestAggregation

    ) {
        $this->videoRequest->update(['status' => 2]);

        $context = [
            'videoRequest' => $this->videoRequest,
            'apiService' => $apiService,
            'emloHelperService' => $emloHelperService,
            'transcriptionService' => $transcriptionService,
            'emloCsvService' => $emloCsvService,
            'llamaService' => $llamaService,
            'postRequestAggregation' => $postRequestAggregation
        ];

        $pipeline = new VideoProcessingPipeline();
        $pipeline->process($context);

        // The pipeline handles all error cases automatically
        // Success case is also handled in the pipeline's then() callback
    }
}