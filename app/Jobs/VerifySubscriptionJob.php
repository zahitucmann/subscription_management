<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Device;
use App\Services\Mock\GoogleService;
use App\Services\Mock\IosService;
use App\Jobs\CallbackJob;
use App\Models\Subscription;

class VerifySubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    private $device_id;
    private $appId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($device_id, $appId)
    {
        $this->device_id = $device_id;
        $this->appId = $appId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device = Device::find($this->device_id);

        if ($device->operating_system == 'android') {
            $service = new GoogleService();
        } else {
            $service = new IosService();
        }

        $service_response = $service->verifyExpiredDevice("1234567896559");
        
        if ($service_response["status"]) {
            Subscription::where('device_id', $this->device_id)->where('appId', $this->appId)->first()->update(array(
                'status' => $service_response["status"],
                'expire_date' => $service_response["expire_date"]
            ));

            CallbackJob::dispatch($this->appId, $this->device_id, 'renewed')->onQueue('callback');
        } else {
            $this->release(180);
        }
    }
}
