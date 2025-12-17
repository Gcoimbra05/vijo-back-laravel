<?php
namespace App\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use App\Attributes\ConsumesUserCredits;
use App\Models\CreditTransaction;
use App\Services\CreditService;
use ReflectionClass;

class DeductJobUserCredits
{
    public function handle(JobProcessed $event)
    {
        $job = $event->job->payload()['data']['command'];
        $jobInstance = unserialize($job);
        
        $reflection = new ReflectionClass($jobInstance);
        $attributes = $reflection->getAttributes(ConsumesUserCredits::class);
        
        if (empty($attributes)) {
            return; // Not a paid job
        }
        
        $creditInfo = $attributes[0]->newInstance();
        $property = $reflection->getProperty('videoRequest');
        $property->setAccessible(true);
        $videoRequest = $property->getValue($jobInstance);
        
        $videoRequest->refresh();
        // Only deduct credits if job succeeded
        if ($videoRequest->status !== 'Approved') {
            return; // Job didn't complete successfully, don't charge
        }
        
        $userId = $videoRequest->user_id;
        
        $transaction = new CreditTransaction;
        $transaction->type = 'deduct';
        $transaction->amount = $creditInfo->cost;
        $transaction->reference = 'process-video-job-completed';
        $transaction->credit_type = 'general_credits';
        
        $creditService = new CreditService;
        $creditService->handleCreditsTransaction($userId, $transaction);
    }
}