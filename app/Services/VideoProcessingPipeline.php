<?php

namespace App\Services;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

use App\Services\VideoRequestProcessing\Steps\ProcessEmotionsStep;
use App\Services\VideoRequestProcessing\Steps\ProcessTranscriptionStep;
use App\Services\VideoRequestProcessing\Steps\ProcessCsvStep;
use App\Services\VideoRequestProcessing\Steps\ProcessLlamaStep;

use App\Services\VideoRequestProcessing\Steps\ProcessInsightsAggregationStep;

class VideoProcessingPipeline
{
    protected $steps = [
        ProcessEmotionsStep::class,
        ProcessTranscriptionStep::class,
        ProcessCsvStep::class,
        ProcessLlamaStep::class,
        ProcessInsightsAggregationStep::class,
        

    ];

    public function process($context)
    {
        return app(Pipeline::class)
            ->send($context)
            ->through($this->steps)
            ->then(function ($context) {
                // Final success handling
                $context['videoRequest']->update(['status' => 2]);
                Log::info('ProcessVideoRequest job completed successfully', [
                    'video_request_id' => $context['videoRequest']->id
                ]);
                return ['success' => true, 'context' => $context];
            });
    }
}