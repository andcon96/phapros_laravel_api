<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResources;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Master\Item;
use App\Models\Master\PrefixIMR;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptFileUpload;
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
            'getHistoryReceipt.getChecklist',
            'getHistoryReceipt.getDocument',
            'getHistoryReceipt.getKemasan',
            'getHistoryReceipt.getTransport',
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
            // $sendemail = (new PurchaseOrderServices())->sendmailapproval($saveddata[1]);

            $datareceipt = ReceiptMaster::with(['getDetailReject','getpo','getTransport','getLaporan.getUserLaporan'])->find($saveddata[1]);
            $totalArrival = $datareceipt->getDetailReject->sum('rcptd_qty_arr');
            $totalApprove = $datareceipt->getDetailReject->sum('rcptd_qty_appr');
            $totalReject = $datareceipt->getDetailReject->sum('rcptd_qty_rej');
            return response()->json([
                "message" => "Success",
                "datareceipt" => $datareceipt,
                "totalArrival" => $totalArrival,
                "totalApprove" => $totalApprove,
                "totalReject" => $totalReject,
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

    public function getprefiximr(Request $request)
    {
        $data = PrefixIMR::get();

        $prefixitem = Item::where('item_code',$request->item)->first();;

        return response()->json(['data' => $data, 'prefixitem' => $prefixitem],200);
    }

    public function getreceipt(Request $request)
    {
        $datareceipt = ReceiptMaster::with(['getDetailReject'])->find(15);
        $totalArrival = $datareceipt->getDetailReject->sum('rcptd_qty_arr');
        $totalApprove = $datareceipt->getDetailReject->sum('rcptd_qty_appr');
        $totalReject = $datareceipt->getDetailReject->sum('rcptd_qty_rej');

        dd($totalArrival, $totalApprove, $totalReject);
    }

    public function saveeditpo(Request $request){
        $data = $request->all();
        
        $saveddata = (new PurchaseOrderServices())->saveEditDetail($data);
        if($saveddata[0] == true){
            // 0 => Status True/False , 1 => Rcpt ID
            $datareceipt = ReceiptMaster::with(['getDetailReject','getpo','getTransport','getLaporan.getUserLaporan'])->find($saveddata[1]);
            $totalArrival = $datareceipt->getDetailReject->sum('rcptd_qty_arr');
            $totalApprove = $datareceipt->getDetailReject->sum('rcptd_qty_appr');
            $totalReject = $datareceipt->getDetailReject->sum('rcptd_qty_rej');
            $fotodelete = json_decode($data['listdeleted']);
            foreach ($fotodelete as $fd){
                ReceiptFileUpload::where('rcptfu_rcpt_id','=',$datareceipt->id)->where('rcptfu_path','=',$fd)->delete();
            }
            return response()->json([
                "message" => "Success",
                "ponbr" => $datareceipt->getpo->po_nbr ?? '',
                "datareceipt" => $datareceipt,
                "totalArrival" => $totalArrival,
                "totalApprove" => $totalApprove,
                "totalReject" => $totalReject,
            ],200);
        }else{
            
            if($saveddata[1] == "over"){
                return response()->json(["message" => "Failed"],501);
            }
            else{
                return response()->json([
                    "message" => "Failed"
                ],400);
            }
        }
    }
}
