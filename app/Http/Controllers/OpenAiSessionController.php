<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiSessionController {
    public function createRealTimeSession()
    {
        try {
            $key = env('OPENAI_API_KEY');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/realtime/sessions', [
                'model' => 'gpt-4o-realtime-preview-2025-06-03',
            ]);

            $responseData = $response->json();
            $secret = env('FRONT_AES_SECRET');
            $encrypted = openssl_encrypt($key, 'AES-128-ECB', $secret);
            $responseData['api_key_encrypted'] = $encrypted;

            return response()->json($responseData, $response->status());
        } catch (\Exception $e) {
            Log::error('Error in /session: ' . $e->getMessage());

            return response()->json([
                'error' => 'Internal Server Error'
            ], 500);
        }
    }
}