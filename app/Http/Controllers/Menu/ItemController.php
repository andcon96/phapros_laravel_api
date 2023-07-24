<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Master\Item;
use App\Services\WSAServices;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request){
        $item = Item::query();
        $listitem = Item::get();

        if($request->listitem){
            $item->where('item_code',$request->listitem);
        }

        $item = $item->paginate(10);
        return view('setting.item.index',compact('item','listitem'));
    }

    public function loaditem(){
        $data = (new WSAServices())->wsaitem();
        if($data === false){
            alert()->error('Error', 'Load Item Failed');
            return back();
        }
        
        return back();
    }

    public function updaternitem(Request $request){
        
        $item = Item::findOrFail($request->e_id);
        $item->item_rn = $request->e_yearrn.$request->e_rn;
        $item->save();

        alert()->success('Success', 'Prefix Item Updated');
        return back();
    }
}
