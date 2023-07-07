<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Menu\ItemController;
use App\Http\Controllers\Menu\PrefixController;
use App\Http\Controllers\Menu\UserController;
use App\Http\Controllers\QxwsaController;
use App\Http\Controllers\Transaksi\PurchaseOrderController;
use App\Http\Controllers\RencanaProduksiController;
use App\Http\Controllers\Transaksi\KetersediaanRawMaterialController;
use App\Http\Controllers\Transaksi\MRPReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return Redirect::to('home');
    }
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    //================================
    // Transaksi
    //================================
    Route::resource('purchaseorder',PurchaseOrderController::class);
    Route::get('viewreceipt',[PurchaseOrderController::class, 'viewreceipt'])->name('viewReceipt');
    Route::get('exportpo',[PurchaseOrderController::class, 'exportpo'])->name('ExportPO');
    Route::get('exportpdfrcp',[PurchaseOrderController::class, 'exportpdfrcp'])->name('ExportReceiptPDF');
    

    //================================
    // Logout & Home 123
    //================================
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
    Route::get('Changepass', [ChangePassword::class,'update'])->name('changepass');

    Route::group(['middleware' => 'can:access_menu_it'], function(){
        //user maintenance
        Route::resource('usermaint', UserController::class);
        //prefix maintenance
        Route::resource('prefixmaint', PrefixController::class);
        //qxwsa maintenance
        Route::resource('qxwsa', QxwsaController::class);
        //item maintenance
        Route::resource('itemmaint', ItemController::class);
        Route::post('loaditem', [ItemController::class, 'loaditem'])->name('LoadItem');
    });

    // Menu Report MRP
    Route::resource('menumrp', MRPReportController::class);
    Route::get('exportmrp', [MRPReportController::class, 'exportmrp'])->name('exportMRP');

    //Report Rencana Produksi
    Route::resource('rencanaProd', RencanaProduksiController::class);
    Route::get('getDetailRencanaProduksi', [RencanaProduksiController::class, 'getDetailRencanaProduksi'])->name('getDetailRencanaProduksi');
    
    //Ketersediaan Raw Material
    Route::resource('KetersediaanRawMaterial', KetersediaanRawMaterialController::class);
    Route::get('exportketersediaanrawmaterial',[KetersediaanRawMaterialController::class, 'exportToExcel'])->name('exportToExcel');
    
});

