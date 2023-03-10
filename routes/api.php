<?php

use App\Http\Controllers\API\APIController;
use App\Http\Controllers\API\PoApiController;
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


Route::middleware('auth:api')->group( function () {
});
route::get('getpo', [PoApiController::class, 'getpo']);

Route::post('login', [APIController::class, 'login']);
route::get('wsapo', [PoApiController::class, 'wsapo']);
