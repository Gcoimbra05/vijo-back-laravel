<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiSessionController {
    public function createRealTimeSession()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/realtime/sessions', [
                'model' => 'gpt-4o-realtime-preview-2025-06-03',
            ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            Log::error('Error in /session: ' . $e->getMessage());

            return response()->json([
                'error' => 'Internal Server Error'
            ], 500);
        }
    }
}