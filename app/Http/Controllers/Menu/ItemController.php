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
}
