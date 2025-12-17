<?php

namespace App\Services;

use App\Models\CompletionsApiInteraction;
use App\Models\RealtimeSessionInteraction;
use App\Models\RealtimeSessionLog;

class OpenAiSessionInteractionService {

    public function storeRealtimeInteraction($sessionId, $data)
    {        
        $interaction = RealtimeSessionInteraction::create([
            'realtime_session_id' => $sessionId,
            'api_response' => json_encode($data),
            'input_tokens' => $data['usage']['input_tokens'],
            'output_tokens' => $data['usage']['output_tokens']
        ]);     
        return $interaction;
    }

    public function storeCompletionsApiInteraction($sessionId, $data)
    {
        $interaction = CompletionsApiInteraction::create([
                'realtime_session_id' => $sessionId,
                'api_response' => json_encode($data),
                'input_tokens' => $data['usage']['prompt_tokens'],
                'output_tokens' => $data['usage']['completion_tokens']
            ]);
        return $interaction;
    }

    public function calculateTokenUsageOfSession($sessionId)
    {
        $realtimeTokensUsed = RealtimeSessionInteraction::where('realtime_session_id', $sessionId)
            ->selectRaw('SUM(input_tokens) as total_input_tokens, SUM(output_tokens) as total_output_tokens')
            ->first();

        $completionsApiTokensUsed = CompletionsApiInteraction::where('realtime_session_id', $sessionId)
            ->selectRaw('SUM(input_tokens) as total_input_tokens, SUM(output_tokens) as total_output_tokens')
            ->first();

        $totalInputTokensUsed = $realtimeTokensUsed->total_input_tokens + $completionsApiTokensUsed->total_input_tokens;
        $totalOutputTokensUsed = $realtimeTokensUsed->total_output_tokens + $completionsApiTokensUsed->total_output_tokens;

        return ['input_tokens' => $totalInputTokensUsed, 'output_tokens' => $totalOutputTokensUsed];
    }

    public function convertTokenUsageIntoCreditCost(array $tokens)
    {
        $inputCredits = (int) ($tokens['input_tokens'] * 0.005);
        $outputCredits = (int) ($tokens['output_tokens'] * 0.015);
        return $inputCredits + $outputCredits;
    }
}