<?php

namespace App\Http\Controllers\Transaksi;

use App\Exports\PurchaseOrderExport;
use App\Http\Controllers\Controller;
use App\Models\Transaksi\PurchaseOrderMaster;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderController extends Controller
{

    public function index(Request $request){

        $po = PurchaseOrderMaster::query();
        $listpo = $po->get();
        $listvend = $po->groupBy('po_vend')->get();
        $listshipto = $po->groupBy('po_ship')->get();


        $pomstr = PurchaseOrderMaster::query()->with('getDetail');
        
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
        return Excel::download(new PurchaseOrderExport($request->domain, $request->ponbr, $request->vendor, $request->shipto, $request->start_orddate, $request->end_orddate), 'Customer Order.xlsx');
    }
}
