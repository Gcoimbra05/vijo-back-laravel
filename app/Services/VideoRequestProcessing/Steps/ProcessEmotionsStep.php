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
        $awsDefaultRegion = config('filesystems.disks.s3.region');
        $awsBucket = config('filesystems.disks.s3.bucket');
        if (!$awsDefaultRegion || !$awsBucket) {
            return ['success' => false, 'error' => 'Emotion processing: AWS S3 configuration is missing'];
        }

        $emloPayload = [
            'url' => $videoUrl,
            'outputType' => 'json',
            'sensitivity' => 'normal'
        ];

        $emloServerUrl = config('services.emlo.server_url') ?? '';
        if (empty($emloServerUrl)) {
            return ['success' => false, 'error' => 'Emotion processing: EMLO server URL is not configured'];
        }

        $emotions = $context['apiService']->sendPost($emloServerUrl, $emloPayload);
        $emotionData = $emotions->getData();
        if (empty($emotionData->response) || $emotionData->response->success !== true) {
            return [
                'success' => false,
                'error' => 'Emotion processing: EMLO server processing failed w/ error: ' .
                ($emotionData->response->error ?? 'Unknown error')
            ];
        }
        
        $rawResponse = json_encode($emotionData->response);

        $emloResponseController = app(\App\Http\Controllers\EmloResponseController::class);
        $newRequest = new Request([
            'request_id' => $context['videoRequest']->id,
            'raw_response' => $rawResponse
        ]);
        $response = $emloResponseController->store($newRequest);

        Log::info('Emotion processing complete for video request: ' . $context['videoRequest']->id);


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