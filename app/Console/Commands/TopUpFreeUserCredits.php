<?php

namespace App\Console\Commands;

use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\CreditTransaction;

use App\Services\CreditService;
use App\Services\MembershipManagementService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;



class TopUpFreeUserCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:top-up-free-user-credits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $freePlan = MembershipPlan::select('id')->where('slug', 'free')->first();

        $usersNeedingTopUp = User::where('plan_id', $freePlan->id)
            ->whereDoesntHave('creditTransactions', function($query) use ($thirtyDaysAgo) {
                $query->whereIn('reference', ['setup-free-plan', 'top-up-free-plan'])
                      ->where('created_at', '>', $thirtyDaysAgo);
            })
            ->whereHas('creditTransactions', function($query) {
                // Ensure they have at least one setup/topup transaction
                $query->whereIn('reference', ['setup-free-plan', 'top-up-free-plan']);
            })
            ->get();
        
        // Also get brand new free users who never got their initial credits
        $newFreeUsers = User::where('plan_id', $freePlan->id)
            ->whereDoesntHave('creditTransactions', function($query) {
                $query->whereIn('reference', ['setup-free-plan', 'top-up-free-plan']);
            })
            ->get();

        $allUsers = $usersNeedingTopUp->merge($newFreeUsers);
        
        $membershipManager = new MembershipManagementService(new CreditService);
        $count = 0;
        foreach ($allUsers as $user) {
            $membershipManager->topUpFreePlan($user);
            $count++;
        }

        $this->info("Topped up {$count} free tier users");
        return 0;
    }
}
