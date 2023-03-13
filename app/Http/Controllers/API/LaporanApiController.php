<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\ReceiptApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Transaksi\LaporanReceiptModel;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptDetail;
use App\Models\Transaksi\ReceiptMaster;
use App\Services\WSAServices;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanApiController extends Controller
{
    public function getreceipt(Request $request)
    {
        $searchrcpt = '';
        
        $data = ReceiptDetail::query()->with(['getMaster','getMaster.getpo','getMaster.getTransport','getMaster.getLaporan'])
        ->selectRaw('
        min(rcptd_rcpt_id) as rcptd_rcpt_id,
        min(rcptd_lot) as rcptd_lot,
        min(rcptd_loc) as rcptd_loc,
        rcptd_part,
        sum(rcptd_qty_arr) as sum_qty_arr,
        sum(rcptd_qty_appr) as sum_qty_appr,
        sum(rcptd_qty_rej) as sum_qty_rej
        ')
        ->where('rcptd_qty_rej','>',0)->groupBy('rcptd_part');
        if($request->receiptnbr){
            $searchrcpt = ReceiptMaster::where('rcpt_nbr','=',$request->receiptnbr)->selectRaw('id')->first();
            if(!$searchrcpt){
                return '';
            }
            $data = $data->where('rcptd_rcpt_id','=',$searchrcpt->id);
        }
        $data = $data->get();
         

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
            $laporanreceipt = new LaporanReceiptModel();
            $laporanreceipt->laporan_rcptnbr = $rcptnbr;
            $laporanreceipt->laporan_ponbr = $ponbr;
            $laporanreceipt->laporan_part = $part;
            $laporanreceipt->laporan_tgl_masuk = $tglmasuk;
            $laporanreceipt->laporan_jmlmasuk = $jmlmasuk;
            $laporanreceipt->laporan_no = $no;
            $laporanreceipt->laporan_lot = $lot;
            $laporanreceipt->laporan_tgl = $tgl;
            $laporanreceipt->laporan_supplier = $supplier;
            $laporanreceipt->laporan_komplain = $komplain;
            $laporanreceipt->laporan_keterangan = $keterangan;
            $laporanreceipt->laporan_komplaindetail = $komplaindetail;
            $laporanreceipt->laporan_angkutan = $angkutan;
            $laporanreceipt->laporan_nopol = $nopol;
            $laporanreceipt->save();
            DB::commit();
            return 'success';
        }
        catch(Exception $err){
            DB::rollback();
            Log::channel('laporan_log')->info('error on : '.$rcptnbr.' '.$err);
            return 'error';
        }
        
    }

}
