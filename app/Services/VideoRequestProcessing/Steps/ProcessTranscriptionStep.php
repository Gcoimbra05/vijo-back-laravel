<?php

namespace App\Services\VideoRequestProcessing\Steps;
use App\Models\Transcript;
use Illuminate\Support\Facades\Log;

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

        $this->storeTranscripts($context['videoRequest']->id, $transcriptionResult, $formattedTranscript);

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

    private function storeTranscripts($requestId, $transcriptContent, $transcriptWEmotions)
    {

        $transcriptText = $transcriptContent['results']['transcripts'][0]['transcript'];
        $data = ['request_id' => $requestId, 'text' => $transcriptText, 'text_w_segment_emotions' => $transcriptWEmotions];
        Transcript::create($data);
    
    }
}