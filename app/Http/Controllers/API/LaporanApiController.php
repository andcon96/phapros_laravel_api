<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\ReceiptApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptDetail;
use App\Models\Transaksi\ReceiptMaster;
use App\Services\WSAServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanApiController extends Controller
{
    public function getreceipt(Request $request)
    {
        $data = ReceiptDetail::query()->with(['getMaster','getMaster.getpo'])
        ->selectRaw('
        min(rcptd_rcpt_id) as rcptd_rcpt_id,
        min(rcptd_lot) as rcptd_lot,
        min(rcptd_loc) as rcptd_loc,
        rcptd_part,sum(rcptd_qty_arr) as sum_qty_arr
        ')
        ->where('rcptd_qty_rej','>',0)->groupBy('rcptd_part')->get();
         

        // if($request->search){
        //     $data->where('po_nbr','LIKE','%'.$request->search.'%')
        //          ->orWhere('po_vend','LIKE','%'.$request->search.'%');
        // }

        // $data = $data->paginate(10);
        
        return $data;
        
    }

    public function submitlaporan(Request $request)
    {
        $rcptnbr = $request->idrcpt;
        $ponbr = $request->ponbr;
        $part = $request->part;
        $tglmasuk = $request->tglmasuk;
        $jmlmasuk = $request->jmlmasuk;
        $no = $request->no;
        $lot = $request->lot;
        $tgl = $request->tgl;
        $supplier = $request->supplier;
        $komplain = $request->komplain;
        $keterangan = $request->keterangan;
        $komplaindetail = $request->komplaindetail;
        $angkutan = $request->angkutan;
        $nopol = $request->nopol;
        
        
        DB::beginTransaction();
        try{

        }
        catch(Exception $err){

        }
        
    }
}
