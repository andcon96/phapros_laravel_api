<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Transaksi\ItemMRP;
use App\Services\WSAServices;
use Illuminate\Http\Request;

class MRPReportController extends Controller
{
    public function index(Request $request)
    {
        $allItems = ItemMRP::get();
        
        $datamrp = (new WSAServices())->wsamrp();
        
        if($request->productline){
            $datamrp = $datamrp->where('t_pt_prod_line','=',$request->productline);
        }
        
        if($request->s_itemcode){
            $datamrp = $datamrp->where('t_mrp_part','=',$request->s_itemcode);
        }

        $datamrp = $datamrp->paginate(15);

        return view('transaksi.mrp.index',compact('datamrp','allItems'));
    }
}
