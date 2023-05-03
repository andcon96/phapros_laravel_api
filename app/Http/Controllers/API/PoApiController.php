<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResources;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptMaster;
use App\Services\PurchaseOrderServices;
use App\Services\WSAServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoApiController extends Controller
{
    public function getpo(Request $request)
    {
        $data = PurchaseOrderMaster::query()
            ->with('getDetail',
            'getApprovalHistReceiptByPO',
            'getHistoryReceipt.getDetail',
            'getHistoryReceipt.getHistoryApproval',
            'getHistoryReceipt.getIsOngoinApproval');

        if($request->search){
            $data->where('po_nbr','LIKE','%'.$request->search.'%')
                 ->orWhere('po_vend','LIKE','%'.$request->search.'%');
        }else{
            // Request Phapros -> Data kosong jika belum search.
            $data->where('po_nbr','');
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
        
        // Save Receipt & Dokumen Dll & Upload Image
        $saveddata = (new PurchaseOrderServices())->savedetail($data);
        if($saveddata[0] == true){

            // 0 => Status True/False , 1 => Rcpt ID
            $sendemail = (new PurchaseOrderServices())->sendmailapproval($saveddata[1]);

            $datareceipt = ReceiptMaster::with(['getDetailReject','getpo','getTransport','getLaporan.getUserLaporan'])->find($saveddata[1]);
            return response()->json([
                "message" => "Success",
                "datareceipt" => $datareceipt
            ],200);
        }else{
            return response()->json([
                "message" => "Failed"
            ],400);
        }
    }

    public function wsaloc(Request $request)
    {
        $data = (new WSAServices())->wsaloc();

        return LocationResources::collection($data);
    }

    public function getreceipt(Request $request)
    {

    }
}
