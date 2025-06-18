<?php

namespace App\Services;

use App\Models\LlmTemplate;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class LlamaService
{
    protected $apiService;
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Process data with Llama.
     *
     * @param string $formattedTranscript
     * @return array
     */
    public function processLlama($llmTemplateId, $formattedTranscript)
    {
        $systemPrompt = '';
        $examples = '';
        $llm_temperature = 0;
        $llm_response_max_length = 0;

        $llmTemplate = LlmTemplate::find($llmTemplateId);
        if (!$llmTemplate){
            return ['success' => false,
                    'error' => 'Llm template with id' . $llmTemplateId . 'doesn\'t exist'];
        }

        if ((($llmTemplate->system_prompt) != null) && (($llmTemplate->examples) != null)
                && (($llmTemplate->llm_temperature) != null) && (($llmTemplate->llm_response_max_length) != null)) {

            $systemPrompt = $llmTemplate->system_prompt;
            $examples = json_decode($llmTemplate->examples);
            $llm_temperature = $llmTemplate->llm_temperature;
            $llm_response_max_length = $llmTemplate->llm_response_max_length;

        } else {
            return ['success' => false,
                    'error' => 'Llm template with id' . $llmTemplateId . 'doesn\'t have all 4 required fields: 
                    system_prompt, examples, llm_temperature and llm_response_max_length'];
        }
   
        $payload = [
            'user_prompt' => $formattedTranscript,
            'system_prompt' => $systemPrompt,
            'examples' => $examples,
            'temperature' => $llm_temperature,
            'max_length' => $llm_response_max_length,
        ];

        $llamaServerUrl = config('services.llama.server_url');
        Log::info('Sending request to Llama server', [
            'llama_server_url' => $llamaServerUrl,
        ]);
        if (!$llamaServerUrl) {
            return ['success' => false,
                    'error' => 'Llama server URL is not configured'];
        }
        $response = $this->apiService->sendPost($llamaServerUrl, $payload);
        $responseData = $response->getData();

        Log::info('Response Data JSON: ' . json_encode($responseData, JSON_PRETTY_PRINT));
        
        // Check if the request was successful
        if ($responseData->success !== true) {
            return ['success' => false,
                    'error' => 'Failed to call Llama server: ' . ($responseData->error ?? 'Unknown error')];
        }
        
        // Return just the data portion of the response
        return ['success' => true, 'response' => $responseData->response->response];
    }

}