<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Subscription;
use Carbon\Carbon;
use App\Jobs\CallbackJob;

class SubscriptionService
{
    public function getSubscription($token)
    {
        $device = Device::where('token', $token)->first();

        if ($device) {
            $subscription = Subscription::where('device_id', $device->id)->where('appId', $device->appId)->first();

            return $subscription ? $subscription : null;
        }

        return null;
    }

    public function createSubscription($request)
    {
        $input = $request->all();
 
        $subscription = Subscription::firstOrCreate(
            ['device_id' =>  $input['device_id'],
            'appId' => $input['appId']],
            [
                'device_id' => $input['device_id'],
                'appId' => $input['appId'],
                'status' => true,
                'expire_date' => new \DateTime(Carbon::now()->addYear(), new \DateTimeZone('GMT-6'))
            ]
        );

        if ($subscription->wasRecentlyCreated === true) {
            $success['expire_date'] =  $subscription->expire_date;
            
            CallbackJob::dispatch($subscription->appId, $subscription->device_id, 'started')->onQueue('callback');

            return $success;
        } else {
            if ($subscription->expire_date < Carbon::now()) {
                $this->updateSubscription($subscription);

                $success['expire_date'] =  $subscription->expire_date;
                return $success;
            }
            
            return null;
        }
    }

    public function updateSubscription($subscription)
    {
        $subscription->update(array(
            'status' => true,
            'expire_date' => new \DateTime(Carbon::now()->addYear(), new \DateTimeZone('GMT-6'))
        ));

        CallbackJob::dispatch($subscription->appId, $subscription->device_id, 'renewed')->onQueue('callback');
    }
}
