<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Http\Resources\ReceiptApiResources;
use App\Http\Resources\WsaPoResources;
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
        min(rcptd_batch) as rcptd_batch

        ');
        
        $data = $data->groupBy('rcptd_part')->groupBy('rcptd_rcpt_id')->get()->take(10);
        return $data;
    }



    public function approvereceipt(Request $request){
        
        $user = $request->userid;
        $receiptnbr = $request->idrcpt;
        
        $receiptdata = ReceiptMaster::with('getpo')->where('rcpt_nbr',$receiptnbr)->first();
        DB::beginTransaction();
        try{
            $datahist = ApprovalHist::where('apphist_receipt_id',$receiptdata->id)->first();
            if($datahist){
                DB::rollback();
                Log::channel('qxtendReceipt')->info('receipt already approved for receipt id: '.$receiptdata->id);
                return 'approve failed';
            }
            $datahist = new ApprovalHist();
            $datahist->apphist_user_id = $user;
            $datahist->apphist_po_domain = $receiptdata->rcpt_domain;
            $datahist->apphist_po_nbr = $receiptdata->getpo->po_nbr;
            $datahist->apphist_status = 'Approved';
            $datahist->apphist_approved_date = Carbon::now()->toDateString();
            $datahist->apphist_receipt_id = $receiptdata->id;
            $datahist->save();
            // $nextappr = ApprovalHist::where('apphist_receipt_id',$receiptdata->id)->where('id','>',$datahist->id)->first();
            // if(!$nextappr){
                $datarcptmstr = ReceiptMaster::with(['getDetail','getpo','getChecklist'])->where('rcpt_nbr',$receiptnbr)->first();
                
                $qxtendreceipt = (new PurchaseOrderServices())->qxPurchaseOrderReceipt($datarcptmstr);
                // $qxtendreceipt = 'success';
                if($qxtendreceipt == 'success'){
                    $datarcptmstr->rcpt_status = 'finished';
                    
                    $datarcptmstr->save();
                    DB::commit();
                    return 'approve success';
                }
                else if($qxtendreceipt == 'failed'){
                    DB::rollback();
                    Log::channel('qxtendReceipt')->info('Approve rcpt_nbr: '.$datarcptmstr['rcpt_nbr'].'qxtend');
                    return 'approve failed';
                }
            // }
            // else{
                // DB::commit();
                // return 'approve success';
            // }
        }
        catch(Exception $err){
            DB::rollback();
            Log::channel('qxtendReceipt')->info('Approve rcpt_nbr: '.$datarcptmstr['rcpt_nbr'].' '.$err);
            
            return 'approve failed';
        }
    }
    public function rejectreceipt(Request $request){
        
        $user = $request->userid;
        $receiptnbr = $request->idrcpt;
        $receiptdata = ReceiptMaster::where('rcpt_nbr',$receiptnbr)->first();
        DB::beginTransaction();
        try{
            $datahist = ApprovalHist::where('apphist_receipt_id',$receiptdata->id)->first();
            if($datahist){
                DB::rollback();
                Log::channel('qxtendReceipt')->info('receipt already rejected for receipt id: '.$receiptdata->id);
                return 'reject failed';
            }
            $datahist = new ApprovalHist();
            $datahist->apphist_user_id = $user;
            $datahist->apphist_po_domain = $receiptdata->rcpt_domain;
            $datahist->apphist_po_nbr = $receiptdata->getpo->po_nbr;
            $datahist->apphist_status = 'Rejected';
            $datahist->apphist_approved_date = Carbon::now()->toDateString();
            $datahist->apphist_receipt_id = $receiptdata->id;
            $datahist->save();

            // $datahist = ApprovalHist::where('apphist_user_id',$user)->where('apphist_receipt_id',$receiptdata->id)->first();
            // $datahist->apphist_status = 'Rejected';
            // $datahist->apphist_approved_date = Carbon::now()->toDateString();
            // $datahist->save();
            $datarcptmstr = ReceiptMaster::where('rcpt_nbr',$receiptnbr)->first();
            $datarcptmstr->rcpt_status = 'rejected';
            $datarcptmstr->save();

            DB::commit();
            return 'reject success';
        }
        catch(Exception $err){
            DB::rollback();
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
            'rcptd_line,
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
            rcptd_part_um')
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
}
