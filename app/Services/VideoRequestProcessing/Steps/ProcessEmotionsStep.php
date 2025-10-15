<?php

namespace App\Services\VideoRequestProcessing\Steps;

use App\Models\Video;
use App\Models\EmloResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessEmotionsStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        $video = Video::firstWhere('request_id', $context['videoRequest']->id);
        $videoUrl = $video->video_url;
        $parsed = parse_url($videoUrl);
        $path = ltrim($parsed['path'], '/');
        $awsDefaultRegion = config('filesystems.disks.s3.region');
        $awsBucket = config('filesystems.disks.s3.bucket');
        Log::info('Processing emotions for video', [
            'video_id' => $video->id,
            'video_url' => $videoUrl,
            'aws_region' => $awsDefaultRegion,
            'aws_bucket' => $awsBucket,
            'path' => $path
        ]);
        if (!$awsDefaultRegion || !$awsBucket) {
            return ['success' => false, 'error' => 'AWS S3 configuration is missing'];
        }

        $emloPayload = [
            'url' => $videoUrl,
            'outputType' => 'json',
            'sensitivity' => 'normal'
        ];

        $emloServerUrl = config('services.emlo.server_url') ?? '';
        Log::info('Sending video to EMLO server for emotion analysis', [
            'emlo_server_url' => $emloServerUrl,
            'payload' => $emloPayload
        ]);
        if (empty($emloServerUrl)) {
            return ['success' => false, 'error' => 'EMLO server URL is not configured'];
        }
        $emotions = $context['apiService']->sendPost($emloServerUrl, $emloPayload);
        $emotionData = $emotions->getData();

        if (empty($emotionData->response) || $emotionData->response->success !== true) {
            return [
                'success' => false,
                'error' => 'EMLO server processing failed w/ error: ' .
                    ($emotionData->response->error ?? 'Unknown error'),
            ];
        }


        Log::info('EMLO analysis complete for video request: ' . $context['videoRequest']->id);
        
        $rawResponse = json_encode($emotionData->response);
        //Log::debug("The raw EMLO response is: " . $rawResponse);

        $emloResponseController = app(\App\Http\Controllers\EmloResponseController::class);
        $newRequest = new Request([
            'request_id' => $context['videoRequest']->id,
            'raw_response' => $rawResponse
        ]);
        // here
        $response = $emloResponseController->store($newRequest);

        return [
            'success' => true,
            'context' => [
                'emotionData' => $emotionData,
                'responseResult' => $response,
                'videoS3ObjectUrl' => $videoUrl
            ]
        ];
    }
}