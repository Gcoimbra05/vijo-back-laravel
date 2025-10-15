<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Spatie\WebhookServer\WebhookCall;
use Illuminate\Support\Facades\Log;

class ApiService
{
    public function sendPost($serverUrl, $payload)
    {
            $response = Http::timeout(120)->post($serverUrl, $payload);
            
            // Check if request was successful
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'response' => $response->json()
                ]);
            }

            // If request failed
            return response()->json([
                'success' => false,
                'message' => 'Failed to call server',
                'error' => $response->body()
            ], $response->status());
    }
}