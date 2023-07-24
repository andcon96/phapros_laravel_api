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
        

        return view('setting.prefix.index',compact('prefix','prefiximr'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     
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
        // dd($request->all());
        $prefix = Prefix::firstOrNew(['id'=>'1']);
        $prefix->prefix_rcpt_pr = $request->prefixrcpt;
        $prefix->prefix_rcpt_rn = $request->rnrcpt;
        $prefix->prefix_ketidaksesuaian = $request->prefixtidaksesuai;
        $prefix->prefix_ketidaksesuaian_rn = $request->rntidaksesuai;
        $prefix->save();
        
        if($request->prefix){
            foreach($request->prefix as $key => $datas){
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Prefix  $prefix
     * @return \Illuminate\Http\Response
     */
    public function show(Prefix $prefix)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Prefix  $prefix
     * @return \Illuminate\Http\Response
     */
    public function edit(Prefix $prefix)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Prefix  $prefix
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prefix $prefix)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Prefix  $prefix
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prefix $prefix)
    {
        //
    }
}
