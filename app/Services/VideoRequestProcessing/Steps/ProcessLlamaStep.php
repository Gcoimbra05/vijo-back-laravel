<?php

namespace App\Services\VideoRequestProcessing\Steps;

class ProcessLlamaStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        $llamaResult = $context['llamaService']->processLlama(
            $context['videoRequest']->llm_template_id,
            $context['formattedTranscript']
        );

        if (isset($llamaResult['error'])) {
            return ['success' => false, 'error' => 'Llama processing: ' . $llamaResult['error']];
        }

        $context['apiService']->sendWebhookNotification(
            'AI processing complete', 
            $context['videoRequest']->id, 
            'video_request', 
            ['response' => $llamaResult['response']]
        );

        return ['success' => true];
    }
}