<?php

namespace App\Services\VideoRequestProcessing\Steps;
use Illuminate\Support\Facades\Log;

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
        
        Log::info('Error during processing of video request: ' . $context['videoRequest']->id . $result['error']);
    }
}