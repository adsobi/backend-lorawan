<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EndNodeController;
use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'api'], function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['middleware' => ['api', 'cors', 'auth:sanctum']], function () {
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::resource('apps', AppController::class)->only('store', 'show', 'index', 'destroy');
    Route::resource('gateways', GatewayController::class)->only('store', 'show', 'index', 'destroy');
    Route::resource('end-nodes', EndNodeController::class)->only('store', 'show', 'index', 'destroy');
});
