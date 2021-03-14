<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseCreateRequest;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Purchase;
use App\Services\PurchaseService;

class PurchaseController extends BaseController
{
    private $purchaseService;

    public function __construct()
    {
        $this->purchaseService = new PurchaseService();
    }

    public function purchase(PurchaseCreateRequest $request)
    {
        $result = $this->purchaseService->createPurchase($request);

        if ($result) {
            return $this->successResponse($result, 'OK');
        }

        return $this->errorResponse('Error', 500);
    }
}
