<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Master\ErrorQxtend;
use App\Models\Master\Item;
use App\Services\WSAServices;
use Illuminate\Http\Request;

class ErrorQxtendController extends Controller
{
    public function index(Request $request){
        $data = ErrorQxtend::query();
        $listerror = ErrorQxtend::select('id','eqa_rcpt_nbr')->groupBy('eqa_rcpt_nbr')->get();

        
        if($request->errorid){
            
            $data->where('eqa_rcpt_nbr',$request->errorid);
        }
        if($request->tanggalkejadian){
            $data->whereDate('created_at',$request->tanggalkejadian);
        }

        $data = $data->orderBy('created_at','desc')->paginate(10);
        
        return view('setting.errorqxtend.index',compact('data','listerror'));
    }

}
