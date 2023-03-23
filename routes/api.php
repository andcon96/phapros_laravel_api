<?php

use App\Http\Controllers\API\APIController;
use App\Http\Controllers\API\LaporanApiController;
use App\Http\Controllers\API\PoApiController;
use App\Http\Controllers\API\ReceiptApiController;
use Illuminate\Http\Request;
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


Route::middleware('auth:api')->group(function () {
    // PO
    route::get('wsapo', [PoApiController::class, 'wsapo']);
    route::get('getpo', [PoApiController::class, 'getpo']);
    route::post('savepo', [PoApiController::class, 'savepo']);
    
    //laporan
    route::post('submitlaporan', [LaporanApiController::class, 'submitlaporan']);
    route::get('getpolaporan', [LaporanApiController::class, 'getreceipt']);

    //receipt approval
    route::post('approvereceipt', [ReceiptApiController::class, 'approvereceipt']);
    route::post('rejectreceipt', [ReceiptApiController::class, 'rejectreceipt']);
    route::get('getreceipt', [ReceiptApiController::class, 'getreceipt']);
});
Route::post('login', [APIController::class, 'login']);


