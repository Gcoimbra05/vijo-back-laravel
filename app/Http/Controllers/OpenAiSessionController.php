<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiSessionController
{
    private function getEncryptedApiKey()
    {
        $key = env('OPENAI_API_KEY');
        $secret = env('FRONT_AES_SECRET');
        return openssl_encrypt($key, 'AES-128-ECB', $secret);
    }

    private function makeOpenAiRequest($method, $endpoint, $data = [])
    {
        $key = env('OPENAI_API_KEY');

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $key,
            'Content-Type'  => 'application/json',
        ])->$method($endpoint, $data);
    }

    public function createRealTimeSession()
    {
        try {
            $response = $this->makeOpenAiRequest(
                'post',
                'https://api.openai.com/v1/realtime/sessions',
                ['model' => 'gpt-4o-realtime-preview-2025-06-03']
            );

            $responseData = $response->json();
            $responseData['api_key_encrypted'] = $this->getEncryptedApiKey();

            return response()->json($responseData, $response->status());
        } catch (\Exception $e) {
            Log::error('Error in createRealTimeSession: ' . $e->getMessage());

            return response()->json([
                'error' => 'Internal Server Error'
            ], 500);
        }
    }

    public function createCompletion(Request $request)
    {
        $messages = $request->input('messages', $request->input('input', []));
        $toolsRaw = $request->input('tools', []);

        $tools = collect($toolsRaw)
            ->map(fn($tool) => $this->normalizeTool($tool))
            ->filter()
            ->values()
            ->all();

        $body = [
            'model' => $request->input('model', 'gpt-4.1'),
            'messages' => $messages,
            'tool_choice' => $request->input('tool_choice', 'auto'),
            'stream' => $request->input('stream', false),
        ];

        if (!empty($tools)) {
            $body['tools'] = $tools;
        }

        if ($request->has('response_format')) {
            $body['response_format'] = $request->input('response_format');
        }

        try {
            $response = $this->makeOpenAiRequest(
                'post',
                'https://api.openai.com/v1/chat/completions',
                $body
            );

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function normalizeTool($tool)
    {
        if (isset($tool['type']) && $tool['type'] === 'function' && isset($tool['function'])) {
            $normalized = $tool;
            if (isset($normalized['function']['parameters'])) {
                $normalized['function']['parameters'] = $this->normalizeParameters($normalized['function']['parameters']);
            }
            return $normalized;
        }

        if (isset($tool['name'])) {
            $parameters = $tool['parameters'] ?? null;
            if ($parameters !== null) {
                $parameters = $this->normalizeParameters($parameters);
            } else {
                $parameters = [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ];
            }

            return [
                'type' => 'function',
                'function' => [
                    'name' => $tool['name'],
                    'description' => $tool['description'] ?? '',
                    'parameters' => $parameters,
                ],
            ];
        }

        if (isset($tool['function']) && !isset($tool['type'])) {
            $normalized = [
                'type' => 'function',
                'function' => $tool['function'],
            ];
            if (isset($normalized['function']['parameters'])) {
                $normalized['function']['parameters'] = $this->normalizeParameters($normalized['function']['parameters']);
            }
            return $normalized;
        }

        return null;
    }

    private function normalizeParameters($parameters)
    {
        if (is_array($parameters) && empty($parameters)) {
            return [
                'type' => 'object',
                'properties' => (object)[],
                'required' => [],
            ];
        }

        if (is_array($parameters)) {
            if (!isset($parameters['type'])) {
                $parameters['type'] = 'object';
            }
            if (!isset($parameters['properties'])) {
                $parameters['properties'] = (object)[];
            }
            if (is_array($parameters['properties']) && empty($parameters['properties'])) {
                $parameters['properties'] = (object)[];
            }
            if (!isset($parameters['required'])) {
                $parameters['required'] = [];
            }
        }

        return $parameters;
    }
}
