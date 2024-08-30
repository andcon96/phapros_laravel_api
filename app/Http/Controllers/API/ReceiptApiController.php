<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\ReceiptApiResources;
use App\Http\Resources\WsaPoResources;
use App\Models\Master\ErrorQxtend;
use App\Models\Transaksi\ApprovalHist;
use App\Models\Transaksi\LaporanReceiptModel;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptDetail;
use App\Models\Transaksi\ReceiptFileUpload;
use App\Models\Transaksi\ReceiptMaster;
use App\Services\PurchaseOrderServices;
use App\Services\WSAServices;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ReceiptApiController extends Controller
{
    public function getreceipt(Request $request)
    {        
        $data = ReceiptDetail::query()->with([
            'getMaster.getpo',
            'getMaster.getTransport',
            'getMaster.getAppr',
            'getMaster.getAppr.getUser',
            'getMaster.getChecklist',
            'getMaster.getDocument',
            'getMaster.getKemasan',
            'getItem'
        ])->whereHas('getMaster',function($r) use($request){
            if($request->rcptnbr){
                $r->where('rcpt_nbr','like','%'.$request->rcptnbr.'%');
            }
            $r->where('rcpt_status','=','created');
        })
        ->selectRaw('
        rcptd_rcpt_id,
        min(rcptd_lot) as rcptd_lot,
        min(rcptd_loc) as rcptd_loc,
        rcptd_part,
        sum(rcptd_qty_arr) as sum_qty_arr,
        sum(rcptd_qty_appr) as sum_qty_appr,
        sum(rcptd_qty_rej) as sum_qty_rej,
        min(rcptd_batch) as rcptd_batch,
        max(rcptd_part_um) as rcptd_part_um,
        max(rcptd_um_pr) as rcptd_um_pr,
        max(rcptd_um_konv) as rcptd_um_konv

        ');
        
        $data = $data->groupBy('rcptd_part')->groupBy('rcptd_rcpt_id')->get();
        return $data;
    }



    public function approvereceipt(Request $request){
        
        $user = $request->userid;
        $receiptnbr = $request->idrcpt;
        $statusapprove = (int)$request->statusapprove;
        
        $receiptdata = ReceiptMaster::with('getpo')->where('rcpt_nbr',$receiptnbr)->first();
        $datahist = ApprovalHist::where('apphist_receipt_id',$receiptdata->id)->first();
        
        if($receiptdata->rcpt_approve_status == 1){
            Log::channel('qxtendReceipt')->info('receipt already approved for receipt id: '.$receiptdata->id);
            return 'approvehist exist';
        }
        else{
            $receiptdata->rcpt_approve_status = 1;
            $receiptdata->save();
        }
        
        DB::beginTransaction();
        try{
            

                $datahist = new ApprovalHist();
                $datahist->apphist_user_id = $user;
                $datahist->apphist_po_domain = $receiptdata->rcpt_domain;
                $datahist->apphist_po_nbr = $receiptdata->getpo->po_nbr;
                $datahist->apphist_status = 'Approved';
                $datahist->apphist_approved_date = Carbon::now()->toDateString();
                $datahist->apphist_receipt_id = $receiptdata->id;
                $datahist->save();

                $datarcptmstr = ReceiptMaster::with(['getDetail','getpo','getChecklist'])->where('rcpt_nbr',$receiptnbr)->first();

                //jika memilih data dikirim ke QAD
                if($statusapprove == 1){
                    
                $qxtendreceipt = (new PurchaseOrderServices())->qxPurchaseOrderReceipt($datarcptmstr);
                
                if($qxtendreceipt[0] == 'success'){
                    $datarcptmstr->rcpt_status = 'finished';
                    $datarcptmstr->rcpt_approve_status = 0;
                    $datarcptmstr->save();

                    DB::commit();
                    return 'approve success';
                }
                else if($qxtendreceipt[0] == 'failed'){
                    DB::rollback();
                    $datarcptmstr->rcpt_approve_status = 0;
                    $datarcptmstr->save();
                    $errordb = new ErrorQxtend();
                    $errordb->eqa_rcpt_id = $datarcptmstr->id;
                    $errordb->eqa_rcpt_nbr = $datarcptmstr->rcpt_nbr;
                    $errordb->eqa_qxtend_message = $qxtendreceipt[1];
                    $errordb->save();
                    Log::channel('qxtendReceipt')->info('Approve rcpt_nbr: '.$datarcptmstr['rcpt_nbr'].'qxtend');
                    return 'approve QAD failed';
                }
            }
            //jika memilih data tidak dikirim ke QAD
            elseif($statusapprove == 0){
                
                $datarcptmstr->rcpt_status = 'finished';
                $datarcptmstr->rcpt_approve_status = 0;
                $datarcptmstr->save();
                DB::commit();
                return 'approve success';

            }

        }
        catch(Exception $err){
            DB::rollback();
            $receiptdata->rcpt_approve_status = 0;
            $receiptdata->save();
            Log::channel('qxtendReceipt')->info('Approve rcpt_nbr: '.$datarcptmstr['rcpt_nbr'].' '.$err);
            return 'approve failed';
        }
    }
    public function rejectreceipt(Request $request){
        
        $user = $request->userid;
        $receiptnbr = $request->idrcpt;
        $receiptdata = ReceiptMaster::where('rcpt_nbr',$receiptnbr)->first();
        $datahist = ApprovalHist::where('apphist_receipt_id',$receiptdata->id)->first();
        
        if($receiptdata->rcpt_approve_status == 1){
            Log::channel('qxtendReceipt')->info('receipt already approved for receipt id: '.$receiptdata->id);
            return 'approvehist exist';
        }
        else{
            $receiptdata->rcpt_approve_status = 1;
            $receiptdata->save();
        }


        DB::beginTransaction();
        try{

            $datahist = new ApprovalHist();
            $datahist->apphist_user_id = $user;
            $datahist->apphist_po_domain = $receiptdata->rcpt_domain;
            $datahist->apphist_po_nbr = $receiptdata->getpo->po_nbr;
            $datahist->apphist_status = 'Rejected';
            $datahist->apphist_approved_date = Carbon::now()->toDateString();
            $datahist->apphist_receipt_id = $receiptdata->id;
            $datahist->save();

            $datarcptmstr = ReceiptMaster::where('rcpt_nbr',$receiptnbr)->first();
            $datarcptmstr->rcpt_status = 'rejected';
            $datarcptmstr->rcpt_approve_status = 0;
            $datarcptmstr->save();

            DB::commit();
            return 'reject success';
        }
        catch(Exception $err){
            DB::rollback();
            $receiptdata->rcpt_approve_status = 0;
            $receiptdata->save();
            Log::channel('qxtendReceipt')->info('Approve rcpt_nbr: '.$datahist['apphist_receipt_id'].' '.$err);
            return 'reject failed';
        }
    }

    public function getreceiptdetail(Request $request)
    {
        $receiptnbr = $request->rcptnbr;
        $data = ReceiptDetail::join('items','item_code','rcptd_part')->whereHas('getMaster',function($q) use($receiptnbr){
            $q->where('rcpt_nbr',$receiptnbr);
        })->selectRaw(
            'rcptd_det.id as iddetail,
            rcptd_rcpt_id,
            rcptd_line,
            rcptd_part,
            rcptd_qty_arr,
            rcptd_qty_appr,
            rcptd_qty_rej,
            rcptd_qty_per_package,
            rcptd_loc,
            rcptd_lot,
            rcptd_batch,
            rcptd_site,
            item_desc,
            rcptd_exp_date,
            rcptd_manu_date,
            rcptd_part_um,
            rcptd_um_pr,
            rcptd_um_konv,
            rcptd_qty_per_package')
        ->orderBy('rcptd_line','asc')
        ->get()
        ->toArray();
        
        return $data;
        
    }

    public function getreceiptfoto(Request $request)
    {
        $receiptnbr = $request->rcptnbr;
        $data = ReceiptFileUpload::whereHas('getMaster',function($q) use($receiptnbr){
            $q->where('rcpt_nbr',$receiptnbr);
        })->selectRaw('rcptfu_path')->orderBy('rcptfu_is_ttd','asc')->get()->toArray();
        
        return $data;
        
    }

    public function getlaporanketidaksesuaian(Request $request)
    {
        $receiptnbr = $request->rcptnbr;
        
        $data = LaporanReceiptModel::with('getfoto')
        ->join(
            DB::Raw('
            (select laporan_rcptnbr as laporan_rcptnbr2,laporan_lot as laporan_lot2,laporan_batch as laporan_batch2,max(updated_at) as updated_at2 from laporan_receipt 
            group by laporan_rcptnbr2,
            laporan_lot2,
            laporan_batch2) receipt2'),
            function($join){
                $join->on('receipt2.laporan_rcptnbr2','=','laporan_rcptnbr');
                $join->on('receipt2.laporan_lot2','=','laporan_lot');
                $join->on('receipt2.laporan_batch2','=','laporan_batch');
                $join->on('receipt2.updated_at2','=','updated_at');
            }
        )
        ->where('laporan_rcptnbr','=',$receiptnbr)
        ->orderBy('id','desc')
        ->selectRaw(
            'id,
            laporan_rcptnbr,
            laporan_imr,
            laporan_lot,
            laporan_batch,
            laporan_jmlmasuk,
            laporan_komplaindetail ,
            laporan_komplain,
            laporan_keterangan')
        // ->get()
        ->get()
        ->toArray();
        
        return $data;
        
    }
}
