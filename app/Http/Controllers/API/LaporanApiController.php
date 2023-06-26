<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\ReceiptApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Transaksi\LaporanImageModel;
use App\Models\Master\Prefix;
use App\Models\Transaksi\LaporanReceiptModel;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptDetail;
use App\Models\Transaksi\ReceiptFileUpload;
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
        
        $data = ReceiptDetail::query()
        ->with([
            'getMaster.getpo',
            'getMaster.getTransport',
            'getMaster.getLaporan.getUserLaporan',
            'getMaster.getChecklist',
            'getItem'
            ])
        ->join('rcpt_mstr','rcpt_mstr.id','rcptd_det.rcptd_rcpt_id')
        ->leftjoin('laporan_receipt',function($join)
        {
            $join->on('rcpt_nbr','=','laporan_rcptnbr');
            $join->on('rcptd_lot','=','laporan_lot');
            $join->on('rcptd_batch','=','laporan_batch');
        })
        ->whereHas('getMaster',function($r) use($request){
            if($request->receiptnbr){
                $r->where('rcpt_nbr','like','%'.$request->receiptnbr.'%');
            }
            $r->where('rcpt_status','=','created');
        })
        ->selectRaw('
        rcpt_nbr,
        rcptd_rcpt_id,
        rcptd_lot,
        rcptd_batch,
        rcptd_loc,
        rcptd_part,
        rcptd_qty_arr as sum_qty_arr,
        rcptd_qty_appr as sum_qty_appr,
        rcptd_qty_rej as sum_qty_rej,
        laporan_komplain,
        laporan_keterangan,
        laporan_tgl,
        laporan_komplaindetail,
        laporan_no
        ')
        ->where('rcptd_qty_rej','>',0);
        
        // ->selectRaw('
        // min(rcptd_rcpt_id) as rcptd_rcpt_id,
        // min(rcptd_lot) as rcptd_lot,
        // min(rcptd_loc) as rcptd_loc,
        // rcptd_part,
        // sum(rcptd_qty_arr) as sum_qty_arr,
        // sum(rcptd_qty_appr) as sum_qty_appr,
        // sum(rcptd_qty_rej) as sum_qty_rej
        // ')
        // ->where('rcptd_qty_rej','>',0)->groupBy('rcptd_rcpt_id')->groupBy('rcptd_part');
        
        // if($request->receiptnbr){
        //     // $searchrcpt = ReceiptMaster::where('rcpt_nbr','=',$request->receiptnbr)->selectRaw('id')->first();
        //     // if(!$searchrcpt){
        //     //     return '';
        //     // }
        //     // $data = $data->where('rcptd_rcpt_id','=',$searchrcpt->id);
        //     $data = $data->where('rcpt_nbr','like','%'.$request->receiptnbr.'%');
        // }

        $data = $data->get()->take(10);
         

        // if($request->search){
        //     $data->where('po_nbr','LIKE','%'.$request->search.'%')
        //          ->orWhere('po_vend','LIKE','%'.$request->search.'%');
        // }

        // $data = $data->paginate(10);
        
        return $data;
        
    }

    public function submitlaporan(Request $request)
    {
        $data = $request->all();
        $rcptnbr = $request->idrcpt;
        $ponbr = $request->ponbr;
        $part = $request->part;
        $tglmasuk = $request->tglmasuk;
        $jmlmasuk = $request->jmlmasuk;
        $no = $request->no;
        $lot = $request->lot;
        $batch = $request->batch;
        $tgl = $request->tgl;
        $supplier = $request->supplier;
        $komplain = $request->komplain;
        $keterangan = $request->keterangan;
        $komplaindetail = $request->komplaindetail;
        $angkutan = $request->angkutan;
        $nopol = $request->nopol;
        $username = $request->username;
        $imr = $request->imr;
        
        DB::beginTransaction();
        try{
            $prefix = Prefix::first();
            $prefixrn = (string)(intval($prefix->prefix_ketidaksesuaian_rn)+1);
            $lenprefixrn = 6 - strlen($prefixrn);
            $prefixfinal = str_pad($prefixrn,$lenprefixrn,'0');
            $prefix->prefix_ketidaksesuaian_rn = $prefixfinal;
            $prefix->save();
            $newprefix = $prefix->prefix_ketidaksesuaian.$prefixfinal;
            
            $laporanreceipt = new LaporanReceiptModel();
            $laporanreceipt->laporan_runningnumber = $newprefix;
            $laporanreceipt->laporan_rcptnbr = $rcptnbr;
            $laporanreceipt->laporan_ponbr = $ponbr;
            $laporanreceipt->laporan_part = $part;
            $laporanreceipt->laporan_tgl_masuk = $tglmasuk;
            $laporanreceipt->laporan_jmlmasuk = $jmlmasuk;
            $laporanreceipt->laporan_no = $no;
            $laporanreceipt->laporan_lot = $lot;
            $laporanreceipt->laporan_batch = $batch;
            $laporanreceipt->laporan_imr = $imr;
            $laporanreceipt->laporan_tgl = $tgl;
            $laporanreceipt->laporan_supplier = $supplier;
            $laporanreceipt->laporan_komplain = $komplain;
            $laporanreceipt->laporan_keterangan = $keterangan;
            $laporanreceipt->laporan_komplaindetail = $komplaindetail;
            $laporanreceipt->laporan_angkutan = $angkutan;
            $laporanreceipt->laporan_nopol = $nopol;
            $laporanreceipt->laporan_anggota = $username;
            $laporanreceipt->save();
            
            if (array_key_exists('images', $data)) {
                foreach ($data['images'] as $key => $dataImage) {
                    if ($dataImage->isValid()) {
                        $dataTime = date('Ymd_His');
                        $filename = $dataTime . '-' . $dataImage->getClientOriginalName();

                        // Simpan File Upload pada Public
                        $savepath = public_path('/uploadfilelaporan/');
                        $dataImage->move($savepath, $filename);

                        $fullfile = $savepath.$filename;
                        
                        $newdata = new LaporanImageModel();
                        $newdata->li_laporan_id = $laporanreceipt->id;
                        $newdata->li_path = '/uploadfilelaporan/'.$filename;
                        
                        $newdata->save();
                    }
                }
            }

            DB::commit();
            return 'success';
        }
        catch(Exception $err){
            DB::rollback();
            Log::channel('laporan_log')->info('error on : '.$rcptnbr.' '.$err);
            return 'error';
        }
        
    }
    
    public function getlaporanfoto(Request $request){
        $receiptnbr = $request->rcptnbr;
        $batch = $request->batch;
        $lot = $request->lot;
        $data = LaporanImageModel::whereHas('getMaster',function($q) use($receiptnbr,$batch,$lot){
            $q->where('laporan_rcptnbr',$receiptnbr);
            $q->where('laporan_batch',$batch);
            $q->where('laporan_lot',$lot);
        })->selectRaw('li_path')->orderBy('li_path','asc')->get()->toArray();
        
        return $data;
    }

}
