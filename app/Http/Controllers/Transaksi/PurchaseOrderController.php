<?php

namespace App\Http\Controllers\Transaksi;

use App\Exports\ExportPOApprovalHistory;
use App\Exports\ExportReceiptDetail;
use App\Exports\PurchaseOrderExport;
use App\Http\Controllers\Controller;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptDetail;
use App\Models\Transaksi\ReceiptMaster;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class PurchaseOrderController extends Controller
{

    public function index(Request $request){

        $po = PurchaseOrderMaster::query()->with('getHistoryReceipt');
        $listpo = $po->get();
        $listvend = $po->groupBy('po_vend')->get();
        $listshipto = $po->groupBy('po_ship')->get();


        $pomstr = PurchaseOrderMaster::query()->with(['getDetail','getHistoryReceipt.getDetailReject','getHistoryReceipt.getChecklist']);
        
        if($request->domain){
            $pomstr->where('po_domain',$request->domain);
        }
        if($request->ponbr){
            $pomstr->where('po_nbr',$request->ponbr);
        }
        if($request->vendor){
            $pomstr->where('po_vend',$request->vendor);
        }
        if($request->shipto){
            $pomstr->where('po_ship',$request->shipto);
        }
        if($request->start_orddate){
            $pomstr->where('po_ord_date','>=',$request->start_orddate);
        }
        if($request->end_orddate){
            $pomstr->where('po_ord_date','<=',$request->end_orddate);
        }
        
        $pomstr = $pomstr->paginate(10);

        return view('transaksi.po.index',compact('pomstr','listpo','listvend','listshipto'));
    }
    
    public function exportpo(Request $request){
        
        if($request->tipe == 1){
            return Excel::download(new PurchaseOrderExport($request->domain, $request->ponbr, $request->vendor, $request->shipto, $request->start_orddate, $request->end_orddate), 'Purchase Order.xlsx');
        }elseif($request->tipe == 2){
            return Excel::download(new ExportPOApprovalHistory($request->domain, $request->ponbr, $request->vendor, $request->shipto, $request->start_orddate, $request->end_orddate), 'Approval History.xlsx');
        }elseif($request->tipe == 3){
            return Excel::download(new ExportReceiptDetail($request->domain, $request->ponbr, $request->vendor, $request->shipto, $request->start_orddate, $request->end_orddate), 'Receipt Detail Purchase Order.xlsx');
        }
        
    }

    public function viewreceipt(Request $request){
        $receipt = ReceiptMaster::query()
                        ->with(['getUser','getChecklist','getDocument','getKemasan','getTransport','getDetail','getpo'])
                        ->where('rcpt_nbr',$request->rcpnbr)
                        ->firstOrFail();
                        
        return view('transaksi.po.receipt.detail-receipt',compact('receipt'));
    }

    public function exportpdfrcp(Request $request){

        $data = ReceiptMaster::query()
                    ->with([
                        'getDetail',
                        'getChecklist',
                        'getDocument',
                        'getKemasan',
                        'getTransport',
                        'getpo',
                        'getUser',
                        'getDetailReject'
                    ])
                    ->where('rcpt_nbr',$request->rcpnbr)
                    ->firstOrFail();
                    
        if($request->tipe == 2){
            $data = ReceiptDetail::query()
                    ->join('rcpt_mstr','rcpt_mstr.id','rcptd_det.rcptd_rcpt_id')
                    ->leftJoin('rcpt_file_upload','rcptfu_rcpt_id','rcpt_mstr.id')
                    ->join('po_mstr','po_mstr.id','rcpt_mstr.rcpt_po_id')
                    ->join('laporan_receipt',function($join){
                        $join->on('rcpt_nbr','=','laporan_rcptnbr');
                        $join->on('rcptd_lot','=','laporan_lot');
                        $join->on('rcptd_batch','=','laporan_batch');
                    })
                    ->where('rcpt_nbr',$request->rcpnbr)
                    ->orderBy('laporan_receipt.created_at','DESC')
                    ->groupBy(['rcpt_nbr', 'rcptd_lot', 'rcptd_batch'])
                    ->get();

            if($data->count() == 0){
                $errorMessage = 'Belum Dibuat Laporan Ketidaksesuaian, Silahkan dibuat terlebihdahulu.';
                return back()->withErrors([$errorMessage]);
            }
            
            $pdf = PDF::loadview(
                'transaksi.po.receipt.exportpdf.blanko',
                [
                    'data' => $data
                ]
            )->setPaper('A4', 'Potrait');

            return $pdf->stream();
        }elseif($request->tipe == 1){
            $pdf = PDF::loadview(
                'transaksi.po.receipt.exportpdf.checklist',
                [
                    'data' => $data
                ]
            )->setPaper('A4','Potrait');

            return $pdf->stream();

            // return view('transaksi.po.receipt.exportpdf.checklist');
        }
    }
}
