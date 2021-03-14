<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\DeviceRegisterRequest;
use App\Models\Device;
use Illuminate\Support\str;

class DeviceController extends BaseController
{
    public function register(DeviceRegisterRequest $request)
    {
        $input = $request->all();
 
        $device = Device::firstOrCreate(
            ['uid' =>  $input['uid']],
            [
                'uid' => $input['uid'],
                'appId' => $input['appId'],
                'operating_system' => $input['operating_system'],
                'language' => $input['language'],
                'token' => (string) Str::uuid()
            ]
        );

        $success['token'] =  $device->token;
        
        return $this->successResponse($success, 'OK');
    }
}
