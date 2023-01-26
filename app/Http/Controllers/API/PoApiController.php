<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoApiResources;
use App\Models\Transaksi\PurchaseOrderMaster;
use Illuminate\Http\Request;

class PoApiController extends Controller
{
    public function getpo(Request $request)
    {
        $data = PurchaseOrderMaster::query()
            ->with('getDetail');

        if($request->search){
            $data->where('po_nbr','LIKE','%'.$request->search.'%')
                 ->orWhere('po_vend','LIKE','%'.$request->search.'%');
        }

        $data = $data->paginate(10);

        return PoApiResources::collection($data);
    }
}
