<?php

namespace App\Services\VideoRequestProcessing\Steps;

use App\Models\LlmResponse;
use Illuminate\Support\Facades\Log;

class ProcessLlamaStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        if (!($context['videoRequest']->llm_template_id)){

            Log::info('Llama processing skipped for video request: ' . $context['videoRequest']->id);
            return ['success' => true];
        }

        $llamaResult = $context['llamaService']->processLlama(
            $context['videoRequest']->llm_template_id,
            $context['formattedTranscript']
        );

        if (isset($llamaResult['error'])) {
            return ['success' => false, 'error' => 'Llama processing: ' . $llamaResult['error']];
        }

        $this->storeLlmResponse($context['videoRequest']->id, $llamaResult['response']);

        Log::info('Llama processing complete for video request: ' . $context['videoRequest']->id);

        return ['success' => true];
    }

    private function storeLlmResponse($requestId, $llmResponse)
    {
        $data = ['request_id' => $requestId, 'text' => $llmResponse];
        LlmResponse::create($data);
    }
}