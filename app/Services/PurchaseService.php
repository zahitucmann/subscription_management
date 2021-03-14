<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Purchase;
use App\Services\Mock\GoogleService;
use App\Services\Mock\IosService;
use Illuminate\Support\str;

class PurchaseService
{
    public function createPurchase($request)
    {
        $device = Device::where('token', $request->token)->first();

        $result = $this->verifyRequest($request, $device);
        
        if ($result) {
            Purchase::create([
                'name' => Str::random(40),
                'device_id' => $device->id
            ]);
            
            return $result;
        }

        return null;
    }

    public function verifyRequest($request, $device)
    {
        if ($device->operating_system == 'android') {
            $mockService = new GoogleService();
        } else {
            $mockService = new IosService();
        }

        $mockResponse = $mockService->validateDevice($request->receipt["dummyString"]);

        if ($mockResponse["status"]) {
            $success['expire_date'] =  $mockResponse["expire_date"];
            $success['status'] =  $mockResponse["status"];

            return $success;
        }


        return null;
    }
}
