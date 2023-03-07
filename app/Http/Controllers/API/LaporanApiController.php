<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\ReceiptApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptMaster;
use App\Services\WSAServices;
use Illuminate\Http\Request;

class LaporanApiController extends Controller
{
    public function getreceipt(Request $request)
    {
        $data = ReceiptMaster::query()
            ->with(['getDetail','getpo'])->get();

        // if($request->search){
        //     $data->where('po_nbr','LIKE','%'.$request->search.'%')
        //          ->orWhere('po_vend','LIKE','%'.$request->search.'%');
        // }

        // $data = $data->paginate(10);
        return $data;
        return ReceiptApiResources::collection($data);
    }

    public function loadlaporan(Request $request)
    {
        $data = (new WSAServices())->wsapo($request->ponbr);

        return WsaPoResources::collection($data);
    }
}
