<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Services\SubscriptionService;
use App\Http\Requests\SubscriptionCreateRequest;
use App\Http\Requests\SubscriptionCheckStatusRequest;
use App\Jobs\CallbackJob;

class SubscriptionController extends BaseController
{
    private $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = new SubscriptionService();
    }

    public function checkSubscription(SubscriptionCheckStatusRequest $request)
    {
        $subscription = $this->subscriptionService->getSubscription($request->token);

        if (!($subscription->status)) {
            CallbackJob::dispatch($subscription->appId, $subscription->device_id, 'canceled')->onQueue('callback');
        }

        $success['subscription_status'] =  $subscription->status;
        
        return $this->successResponse($success, 'OK');
    }

    public function createSubscription(SubscriptionCreateRequest $request)
    {
        $result = $this->subscriptionService->createSubscription($request);
        
        if ($result) {
            return $this->successResponse($result, 'OK');
        }

        return $this->errorResponse('Error', 500);
    }
}
