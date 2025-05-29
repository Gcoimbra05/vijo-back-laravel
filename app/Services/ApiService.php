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

    public function sendWebhookNotification($eventType, $resourceId, $resourceType = 'video_request', array $additionalData = [])
    {
        $webhookUrl = env("WEBHOOK_URL");
        $payload = array_merge(['event' => $eventType, "{$resourceType}_id" => $resourceId], $additionalData);
        
        Log::info('Sending webhook notification', [
            "{$resourceType}_id" => $resourceId,
            'event_type' => $eventType,
            'webhook_url' => $webhookUrl,
            'payload' => $payload
        ]);
        
        try {
            WebhookCall::create()
                ->url($webhookUrl)
                ->doNotSign()
                ->payload($payload)
                ->dispatchSync();
                
            Log::info('Webhook notification sent successfully', [
                "{$resourceType}_id" => $resourceId,
                'event_type' => $eventType
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook notification sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook notification failed', [
                "{$resourceType}_id" => $resourceId,
                'event_type' => $eventType,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Webhook notification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}