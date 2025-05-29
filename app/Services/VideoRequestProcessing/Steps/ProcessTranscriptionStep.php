<?php

namespace App\Services\VideoRequestProcessing\Steps;

class ProcessTranscriptionStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        $transcriptionResult = $context['transcriptionService']->transcribeSync(
            'test_job' . $context['videoRequest']->id,
            env('S3_VIDEO_URL'),
            'en-US',
            10,
            10
        );

        if (isset($transcriptionResult['error'])) {
            return ['success' => false, 'error' => 'Transcript processing: ' . $transcriptionResult['error']];
        }

        $formattedTranscript = $context['transcriptionService']->formatTranscriptForLlm(
            $context['emotionData'], 
            $transcriptionResult
        );

        $context['apiService']->sendWebhookNotification(
            'transcription complete', 
            $context['videoRequest']->id, 
            'video_request', 
            ["transcript" => $formattedTranscript]
        );

        return [
            'success' => true,
            'context' => [
                'formattedTranscript' => $formattedTranscript,
                'transcriptionResult' => $transcriptionResult
            ]
        ];
    }
}