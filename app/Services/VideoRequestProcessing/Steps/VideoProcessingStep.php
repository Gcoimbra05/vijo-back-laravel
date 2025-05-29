<?php

namespace App\Services\VideoRequestProcessing\Steps;

abstract class VideoProcessingStep
{
    public function handle($context, $next)
    {
        try {
            $result = $this->execute($context);
            
            if (!$result['success']) {
                $this->handleError($context, $result);
                return $result;
            }
            
            // Merge any new context data
            if (isset($result['context'])) {
                $context = array_merge($context, $result['context']);
            }
            
            return $next($context);
            
        } catch (\Exception $e) {
            $error = ['success' => false, 'error' => $e->getMessage()];
            $this->handleError($context, $error);
            return $error;
        }
    }

    abstract protected function execute($context);

    protected function handleError($context, $result)
    {
        $context['videoRequest']->update([
            'status' => 3, 
            'error' => $result['error']
        ]);
        
        $context['apiService']->sendWebhookNotification(
            'error', 
            $context['videoRequest']->id, 
            'video_request', 
            ['message' => $result['error']]
        );
    }
}