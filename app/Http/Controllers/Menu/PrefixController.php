<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Master\Prefix;
use App\Models\Master\PrefixIMR;
use Illuminate\Http\Request;

class PrefixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prefix = Prefix::first();

        $prefiximr = PrefixIMR::get();


        return view('setting.prefix.index', compact('prefix', 'prefiximr'));
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $prefix = Prefix::firstOrNew(['id' => '1']);
        $prefix->prefix_rcpt_pr = $request->prefixrcpt;
        $prefix->prefix_rcpt_rn = $request->rnrcpt;
        $prefix->prefix_ketidaksesuaian = $request->prefixtidaksesuai;
        $prefix->prefix_ketidaksesuaian_rn = $request->rntidaksesuai;
        $prefix->save();

        if ($request->prefix) {
            foreach ($request->prefix as $key => $datas) {
                $prefiximr = new PrefixIMR();
                $prefiximr->pin_prefix = $datas;
                $prefiximr->pin_rn = '000000';
                $prefiximr->save();
            }
        }

        //edit imr
        if($request->prefixedit){
            foreach($request->prefixedit as $key => $dataedit){
                $dataprefiximr = PrefixIMR::where('pin_prefix',$dataedit)->first();
                $dataprefiximr->pin_rn = $request->rnedit[$key];
                $dataprefiximr->save();
            }
        }

        alert()->success('Success', 'Prefix Updated');
        return back();
        //
    }

    public function updaternimr(Request $request)
    {

        $prefiximr = PrefixIMR::findOrFail($request->e_id);
        $prefiximr->pin_prefix = $request->e_prefix;
        $prefiximr->pin_rn = $request->e_yearrn.$request->e_rn;
        $prefiximr->save();

        alert()->success('Success', 'Prefix IMR Updated');
        return back();
    }
}
