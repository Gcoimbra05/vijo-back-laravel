<?php

namespace App\Services;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

use App\Services\VideoRequestProcessing\Steps\ProcessEmotionsStep;
use App\Services\VideoRequestProcessing\Steps\ProcessTranscriptionStep;
use App\Services\VideoRequestProcessing\Steps\ProcessInsightsAggregationStep;

class VideoProcessingPipeline
{
    protected $steps = [
        ProcessEmotionsStep::class,
        ProcessTranscriptionStep::class,
        ProcessInsightsAggregationStep::class,
    ];

    public function process($context)
    {
        $context['videoRequest']->update(['status' => 'Accept']);

        $result = app(Pipeline::class)  // Store the result first
            ->send($context)
            ->through($this->steps)
            ->then(function ($context) {
                // Final success handling
                $context['videoRequest']->update(['status' => 'Approved']);
                Log::info('ProcessVideoRequest job completed successfully', [
                    'video_request_id' => $context['videoRequest']->id
                ]);
                return ['success' => true, 'context' => $context];
            });
        
        if (is_array($result) && isset($result['success']) && !$result['success']) {
            Log::info('VideoProcessingPipeline stopped early', [
                'video_request_id' => $context['videoRequest']->id,
                'reason' => $result['reason'] ?? 'unknown'
            ]);
            $context['videoRequest']->update(['status' => 'Error']);
            return $result;

        }
        return $result;
    }
}