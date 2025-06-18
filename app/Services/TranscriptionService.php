<?php

namespace App\Services;

use Aws\Exception\AwsException;
use Aws\TranscribeService\TranscribeServiceClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TranscriptionService
{
    protected $client;

    public function __construct()
    {
        $awsDefaultRegion = config('filesystems.disks.s3.region');
        $awsAccessKeyId = config('services.ses.key');
        $awsSecretKeyId = config('services.ses.secret');
        if (!$awsDefaultRegion || !$awsAccessKeyId || !$awsSecretKeyId) {
            Log::error('AWS Transcribe configuration is missing. Please check your .env file or config/services.php');
            throw new \Exception('AWS Transcribe configuration is missing.');
        }

        $this->client = new TranscribeServiceClient([
            'version' => 'latest',
            'region' => $awsDefaultRegion,
            'credentials' => [
                'key' => $awsAccessKeyId,
                'secret' => $awsSecretKeyId,
            ]
        ]);
    }

    /**
     * Start a transcription job and wait for it to complete
     * 
     * @param string $jobName
     * @param string $mediaUrl
     * @param string $languageCode
     * @param int $pollingInterval Seconds between status checks
     * @param int $maxAttempts Maximum number of polling attempts
     * @return array|string Transcript text if successful, error array otherwise
     */
    public function transcribeSync($jobName, $mediaUrl, $languageCode = 'en-US', $pollingInterval = 10, $maxAttempts = 60)
    {
        // Start the job
        $startResult = $this->startTranscriptionJob($jobName, $mediaUrl, $languageCode);
        
        if (isset($startResult['error'])) {            
            return ['success' => false,
                    'error' => 'Failed to start transcription job' . $jobName . ':' . $startResult['message']];
        }
                
        // Poll for completion
        $attempts = 0;
        while ($attempts < $maxAttempts) {
            $status = $this->getTranscriptionJob($jobName);
            
            if (isset($status['error'])) {
                return ['success' => false,
                        'error' => 'Error checking transcription status:' . $status['message']];
            }
            
            $jobStatus = $status['TranscriptionJob']['TranscriptionJobStatus'];
            
            if ($jobStatus === 'COMPLETED') {
                Log::info("Transcription job completed: {$jobName}");
                $transcriptUrl = $status['TranscriptionJob']['Transcript']['TranscriptFileUri'];
                $transcriptContent = $this->fetchTranscriptContent($transcriptUrl);
                return $transcriptContent;
            } 
            
            if ($jobStatus === 'FAILED') {
                $reason = $status['TranscriptionJob']['FailureReason'] ?? 'Unknown reason';
                Log::error("Transcription job failed: {$jobName}. Reason: {$reason}");
                return [
                    'success' => false,
                    'error' => "Transcription failed: {$reason}"
                ];
            }
            
            // Wait before checking again
            sleep($pollingInterval);
            $attempts++;
        }
        
        // If we reach here, we've timed out
        Log::warning("Transcription job timed out: {$jobName}");
        return [
            'success' => false,
            'error' => "Transcription job timed out after " . ($pollingInterval * $maxAttempts) . " seconds"
        ];
    }

    public function startTranscriptionJob($jobName, $mediaUrl, $languageCode = 'en-US')
    {
        try {
            $result = $this->client->startTranscriptionJob([
                'TranscriptionJobName' => $jobName,
                'Media' => [
                    'MediaFileUri' => $mediaUrl,
                ],
                'MediaFormat' => $this->getMediaFormat($mediaUrl),
                'LanguageCode' => $languageCode,
            ]);
            
            return $result;
        } catch (AwsException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getTranscriptionJob($jobName)
    {
        try {
            $result = $this->client->getTranscriptionJob([
                'TranscriptionJobName' => $jobName,
            ]);
            
            return $result;
        } catch (AwsException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function fetchTranscriptContent($url)
    {
        try {
            $client = new Client();
            $response = $client->get($url);
            $content = json_decode($response->getBody()->getContents(), true);
            
            // AWS Transcribe places the transcript in this structure
            if (isset($content['results']['transcripts'][0]['transcript'])) {
                return $content;
            }
            
            return [
                'error' => true,
                'message' => 'Transcript content could not be parsed'
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Failed to fetch transcript: ' . $e->getMessage(),
            ];
        }
    }

    private function getMediaFormat($mediaUrl)
    {
        $extension = pathinfo($mediaUrl, PATHINFO_EXTENSION);
        $formats = [
            'mp3' => 'mp3',
            'mp4' => 'mp4',
            'wav' => 'wav',
            'flac' => 'flac',
            'ogg' => 'ogg',
            'amr' => 'amr',
            'webm' => 'webm',
        ];
        
        return $formats[$extension] ?? 'mp3'; // Default to mp3 if format not recognized
    }
    public function formatTranscriptForLlm($requestResponse, $transcriptResponse) {
        if (!empty($requestResponse->response->data) && !empty($requestResponse->response->data->segments) && !empty($requestResponse->response->data->segments->data)) {
            $segments = $requestResponse->response->data->segments->data;
            Log::info('Number of segments to process: ' . count($segments));

            // Initialize constructed_transcript for each segment
            foreach ($segments as &$row) {
                $row['constructed_transcript'] = '';
            }
            unset($row); // Unset reference to avoid accidental modifications

            // Process each transcription item
            foreach ($transcriptResponse['results']['items'] as $item) {
                if (isset($item['start_time']) && isset($item['alternatives']) && !empty($item['alternatives'])) {
                    $itemStartTime = (float)$item['start_time'];
                    $closestRow = null;
                    $minDistance = PHP_FLOAT_MAX;
                    
                    // Find the closest row based on time proximity
                    foreach ($segments as $index => $row) {
                        $rowStartTime = (float)$row[2];
                        $rowEndTime = (float)$row[3];
                        
                        // If the item is directly within this segment's time range, it's an exact match
                        if ($itemStartTime >= $rowStartTime && $itemStartTime <= $rowEndTime) {
                            $closestRow = $index;
                            break; // Exact match found, no need to continue searching
                        }
                        
                        // Calculate distance to the middle of the segment
                        $rowMidTime = ($rowStartTime + $rowEndTime) / 2;
                        $distance = abs($itemStartTime - $rowMidTime);
                        
                        if ($distance < $minDistance) {
                            $minDistance = $distance;
                            $closestRow = $index;
                        }
                    }
                    
                    // Add the content to the closest row's transcript
                    if ($closestRow !== null) {
                        $content = $item['alternatives'][0]['content'] ;
                        
                        // Add a space if the transcript isn't empty
                        if (!empty($segments[$closestRow]['constructed_transcript'])) {
                            $segments[$closestRow]['constructed_transcript'] .= ' ';
                        }
                        
                        $segments[$closestRow]['constructed_transcript'] .= $content;
                    }
                }
            }
            
            $whole_constructed_transcript = '';
            // Log the constructed transcripts
            foreach ($segments as $index => $row) {
                if (!empty($row['constructed_transcript'])) {
                    $row['constructed_transcript'] .= $row[116];
                    $whole_constructed_transcript .= $row['constructed_transcript'];
                    //Log::info("Segment {$index} CONSTRUCTED TRANSCRIPT: " . $row['constructed_transcript']);
                } else {
                    //Log::info("Segment {$index} has no transcript");
                }
            }
            
            Log::info("whole constructed transcript is: " . $whole_constructed_transcript);
            return $whole_constructed_transcript; // Return the modified data
        }
    }
}