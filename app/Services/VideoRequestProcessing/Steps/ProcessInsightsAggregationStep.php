<?php

namespace App\Services\VideoRequestProcessing\Steps;

use App\Models\VideoRequest;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class ProcessInsightsAggregationStep extends VideoProcessingStep {

    protected $apiService;
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

   public function execute($context)
    {
        Log::debug('postRequestAggregation START');

        $userId = VideoRequest::select('user_id')
            ->where('id', $context['videoRequest']->id)
            ->first();
        if (!$userId) {
            return ['success' => false, 'error' => 'Post request aggregation: internal server error'];
        }

        $context['postRequestAggregation']->aggregationPipeline(
            $context['videoRequest']->id,
            $userId->user_id
        );

        Log::info('Post request aggregation complete for video request: ' . $context['videoRequest']->id);

        return ['success' => true];
    }     
}