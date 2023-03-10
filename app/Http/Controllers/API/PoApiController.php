<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Services\PurchaseOrderServices;
use App\Services\WSAServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoApiController extends Controller
{
    public function getpo(Request $request)
    {
        $data = PurchaseOrderMaster::query()
            ->with('getDetail');

        if($request->search){
            $data->where('po_nbr','LIKE','%'.$request->search.'%')
                 ->orWhere('po_vend','LIKE','%'.$request->search.'%');
        }

        $data = $data->paginate(10);

        return PoApiResources::collection($data);
    }

    public function wsapo(Request $request)
    {
        $data = (new WSAServices())->wsapo($request->ponbr);

        return WsaPoResources::collection($data);
    }

    public function savepo(Request $request)
    {
        $data = $request->all();

        Log::channel('savepo')->info(json_encode($data));

        // Save Receipt & Dokumen Dll
        $saveddata = (new PurchaseOrderServices())->savedetail($data);
        
        if($saveddata == true){
            return response()->json([
                "message" => "Success"
            ],200);
        }else{
            return response()->json([
                "message" => "Failed"
            ],400);
        }
    }
}
