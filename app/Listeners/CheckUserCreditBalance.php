<?php
namespace App\Listeners;

use Illuminate\Queue\Events\JobProcessing;
use App\Attributes\ConsumesUserCredits;
use App\Models\UserCredit;
use ReflectionClass;

class CheckUserCreditBalance
{
    public function handle(JobProcessing $event)
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
        $userId = $videoRequest->user_id;
        
        $userCredits = UserCredit::where('user_id', $userId)->first();
        
        // Check balance but DON'T deduct yet
        if ($userCredits->general_credit_balance < $creditInfo->cost) {
            $videoRequest->update([
                'status' => 'Reject',
                'error_message' => 'Insufficient credits'
            ]);
            $event->job->delete();
            return;
        }
    }
}