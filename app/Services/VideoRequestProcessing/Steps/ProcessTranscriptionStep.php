<?php

namespace App\Services\VideoRequestProcessing\Steps;
use App\Models\Transcript;
use App\Models\Video;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessTranscriptionStep extends VideoProcessingStep
{
    protected function execute($context)
    {
        $video = Video::firstWhere('request_id', $context['videoRequest']->id);

        $disk = config('filesystems.default', 's3');
        $videoStoragePath = 'videos/' . $video->video_name;
        $videoUrl = Storage::disk($disk)->url($videoStoragePath);
        if (!$videoUrl) {
            return ['success' => false, 'error' => 'Video URL not found'];
        }

        Log::info('Processing transcription for video', [
            'video_id' => $video->id,
            'video_url' => $videoUrl
        ]);

        $transcriptionResult = $context['transcriptionService']->transcribeSync(
            'test_job' . date('YmdHis'),
            $videoUrl,
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