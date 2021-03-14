<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\EndpointController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [DeviceController::class, 'register']);
Route::post('purchase', [PurchaseController::class, 'purchase']);
Route::get('checkSubscription', [SubscriptionController::class, 'checkSubscription']);
Route::post('createSubscription', [SubscriptionController::class, 'createSubscription']);

Route::resource('applications', ApplicationController::class);
Route::resource('endpoints', EndpointController::class);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
