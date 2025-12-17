<?php

namespace App\Http\Controllers;

use App\Services\OpenAiSessionInteractionService;
use Illuminate\Http\Request;

class OpenAiSessionLoggingController {
    public function __construct(protected OpenAiSessionInteractionService $interactionService){}

    public function storeRealtimeSessionResponse(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required',
            
            // Response object
            'response' => 'required|array',
            'response.id' => 'required|string',
            'response.conversation_id' => 'required|string',
            'response.max_output_tokens' => 'required|string',
            'response.metadata' => 'nullable',
            'response.modalities' => 'required|array',
            'response.modalities.*' => 'string|in:audio,text',
            'response.object' => 'required|string|in:realtime.response',
            
            // Output array - can contain different item types
            'response.output' => 'required|array',
            'response.output.*.id' => 'required|string',
            'response.output.*.object' => 'required|string|in:realtime.item',
            'response.output.*.type' => 'required|string|in:message,function_call,function_call_output', // Added function_call types
            'response.output.*.status' => 'required|string|in:completed,in_progress,incomplete',
            
            // Message-specific fields (only for type=message)
            'response.output.*.role' => 'nullable|string|in:assistant,user',
            'response.output.*.content' => 'nullable|array',
            'response.output.*.content.*.type' => 'nullable|string|in:audio,text',
            'response.output.*.content.*.transcript' => 'nullable|string',
            
            // Function call-specific fields (only for type=function_call)
            'response.output.*.name' => 'nullable|string', // Function name
            'response.output.*.call_id' => 'nullable|string', // Call ID
            'response.output.*.arguments' => 'nullable|string', // JSON string of arguments
            
            // Output audio format
            'response.output_audio_format' => 'required|string|in:pcm16,g711_ulaw,g711_alaw',
            
            // Status fields
            'response.status' => 'required|string|in:completed,in_progress,incomplete,failed,cancelled',
            'response.status_details' => 'nullable',
            
            // Usage object
            'response.usage' => 'required|array',
            'response.usage.input_tokens' => 'required|integer|min:0',
            'response.usage.output_tokens' => 'required|integer|min:0',
            
            // Voice
            'response.voice' => 'required|string',
        ]);
        $this->interactionService->storeRealtimeInteraction($request->session_id, $validated['response']);
    }

    public function storeCompletionsApiResponse(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required',

            'response.id' => 'required|string',
            'response.object' => 'required|string',
            'response.created' => 'required|integer',
            'response.model' => 'required|string',
            'response.choices' => 'required|array',
            'response.choices.*.index' => 'required|integer',
            'response.choices.*.message' => 'required|array',
            'response.choices.*.message.role' => 'required|string',
            'response.choices.*.message.content' => 'nullable|string',
            'response.choices.*.message.tool_calls' => 'nullable|array',
            'response.choices.*.message.tool_calls.*.id' => 'required|string',
            'response.choices.*.message.tool_calls.*.type' => 'required|string',
            'response.choices.*.message.tool_calls.*.function' => 'required|array',
            'response.choices.*.message.tool_calls.*.function.name' => 'required|string',
            'response.choices.*.message.tool_calls.*.function.arguments' => 'required|string',
            'response.choices.*.message.refusal' => 'nullable|string',
            'response.choices.*.message.annotations' => 'nullable|array',
            'response.choices.*.logprobs' => 'nullable',
            'response.choices.*.finish_reason' => 'required|string',
            'response.usage' => 'required|array',
            'response.usage.prompt_tokens' => 'required|integer',
            'response.usage.completion_tokens' => 'required|integer',
            'response.usage.total_tokens' => 'required|integer',
            'response.usage.prompt_tokens_details' => 'required|array',
            'response.usage.prompt_tokens_details.cached_tokens' => 'required|integer',
            'response.usage.prompt_tokens_details.audio_tokens' => 'required|integer',
            'response.usage.completion_tokens_details' => 'required|array',
            'response.usage.completion_tokens_details.reasoning_tokens' => 'required|integer',
            'response.usage.completion_tokens_details.audio_tokens' => 'required|integer',
            'response.usage.completion_tokens_details.accepted_prediction_tokens' => 'required|integer',
            'response.usage.completion_tokens_details.rejected_prediction_tokens' => 'required|integer',
            'response.service_tier' => 'required|string',
            'response.system_fingerprint' => 'required|string',
        ]);

        
        $this->interactionService->storeCompletionsApiInteraction($request->session_id, $validated['response']);
    }

}