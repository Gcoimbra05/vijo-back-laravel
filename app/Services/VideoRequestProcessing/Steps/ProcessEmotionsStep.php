<?php

namespace App\Services\VideoRequestProcessing\Steps;

use App\Models\Video;
use App\Models\EmloResponse;

class ProcessEmotionsStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        $video = Video::firstWhere('request_id', $context['videoRequest']->id);
        $videoUrl = $video->video_url;
        $parsed = parse_url($videoUrl);
        $path = ltrim($parsed['path'], '/');
        $videoS3ObjectUrl = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/' . $path;

        $emloPayload = [
            'url' => $videoS3ObjectUrl,
            'outputType' => 'json',
            'sensitivity' => 'normal'
        ];

        $emotions = $context['apiService']->sendPost(env('EMLO_SERVER_URL'), $emloPayload);
        $emotionData = $emotions->getData();

        if ($emotionData->success !== true) {
            return ['success' => false, 'error' => 'EMLO server processing failed w/ error: ' . $emotionData->error];
        }

        $context['apiService']->sendWebhookNotification('emotional analysis complete', $context['videoRequest']->id, 'video_request');
        
        $emloResponse = new EmloResponse();
        $rawResponse = json_encode($emotionData->response);
        $responseResult = $emloResponse->store($rawResponse, $context['videoRequest']->id);
        $context['emloResponseService']->extractAndStorePathValues($rawResponse, $responseResult['id']);

        return [
            'success' => true,
            'context' => [
                'emotionData' => $emotionData,
                'responseResult' => $responseResult,
                'videoS3ObjectUrl' => $videoS3ObjectUrl
            ]
        ];
    }
}