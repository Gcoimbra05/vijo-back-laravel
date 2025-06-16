<?php

namespace App\Services\VideoRequestProcessing\Steps;

use App\Http\Controllers\CsvController;
use Illuminate\Http\Request;

class ProcessCsvStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        $csvResult = $context['emloCsvService']->createCsvFile($context['emotionData']);
        
        if (isset($csvResult['error'])) {
            return ['success' => false, 'error' => 'CSV creation: ' . $csvResult['error']];
        }

        $responseData = json_decode($context['responseResult']->getContent(), true);
        $responseId = $responseData['results']['emlo_response']['id'];

        $request = new Request([
            'response_id' => $responseId,
            'csv_path' => $csvResult['csvFilePath'],
        ]);

        $csvController = app(CsvController::class);
        $csvUploadResponse = $csvController->uploadAndStore($request);

        // Handle CSV upload response error checking here if needed
        $responseData = json_decode($csvUploadResponse->getContent(), true);
        $csvS3Url = $responseData['data']['s3_url'];
        
        $context['apiService']->sendWebhookNotification(
            'csv complete', 
            $context['videoRequest']->id, 
            'video_request', 
            ['url' => $csvS3Url]
        );

        return [
            'success' => true,
            'context' => ['csvS3Url' => $csvS3Url]
        ];
    }
}