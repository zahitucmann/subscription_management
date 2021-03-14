<?php

namespace App\Services\Mock;

use Carbon\Carbon;

class GoogleService
{
    public function validateDevice($dummy_string)
    {
        if (substr($dummy_string, -1) % 2 != 0) {
            return [
                'status' => true,
                'expire_date' =>  new \DateTime(Carbon::now()->addYear(), new \DateTimeZone('GMT-6'))
            ];
        }

        return null;
    }

    public function verifyExpiredDevice($dummy_string)
    {
        if (substr($dummy_string, -2) % 6 != 0) {
            return [
                'status' => true,
                'expire_date' =>  new \DateTime(Carbon::now()->addYear(), new \DateTimeZone('GMT-6'))
            ];
        }

        return ['status_code' => 429];
    }
}
