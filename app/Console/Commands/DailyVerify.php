<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Jobs\VerifySubscriptionJob;
use App\Models\Subscription;

class DailyVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify again expired subscription in daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subscriptions = Subscription::whereDate('expire_date', '<', Carbon::now())->get();

        foreach ($subscriptions as $subscription) {
            VerifySubscriptionJob::dispatch($subscription->device_id, $subscription->appId)->onQueue('verifySubscription');
        }
    }
}
