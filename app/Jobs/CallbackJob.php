<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Endpoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Device;

class CallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $appId;
    private $device_id;
    private $event;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appId, $device_id, $event)
    {
        $this->appId = $appId;
        $this->device_id = $device_id;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $endpoint = Endpoint::where('appId', $this->appId)->first();
        
        $response = Http::post($endpoint->endpoint, [
            'appId' => $this->appId,
            'device_id' => $this->device_id,
            'event' => $this->event
        ]);

        if (!$response->successful()) {
            $this->release(180);
        } else {
            DB::table('subscription_changes')->insert([
                'appId' => $this->appId,
                'operating_system' => Device::find($this->device_id)->operating_system,
                'event' => $this->event,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            // example queries
            //SELECT count(*) FROM subscription_management.subscription_changes where appId="1" and operating_system="ios" and event="started"
            //SELECT count(*) FROM subscription_management.subscription_changes where event="started" and created_at between "2021-03-14" and  "2021-03-15"
        }
    }
}
