<?php

namespace App\Http\Controllers;

use App\Models\Master\Qxwsa;
use Illuminate\Http\Request;

class QxwsaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = Qxwsa::first();

        return view('setting.qxwsa.wsas',['data' => $data]);
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
        //
        $this->validate($request, [
            'wsaurl' => 'required',
            'wsapath' => 'required',
        ]);
        
        $qxwsa = Qxwsa::firstOrNew(array('id' => '1'));
        $qxwsa->wsas_domain = $request->domain;
        $qxwsa->wsas_url = $request->wsaurl;
        $qxwsa->wsas_path = $request->wsapath;
        $qxwsa->qx_enable = $request->qxenable;
        $qxwsa->qx_url = $request->qxurl;
        $qxwsa->qx_path = $request->qxpath;
        $qxwsa->save();

        // $request->session()->flash('updated', 'QX WSA Successfully Updated');
        alert()->success('Success', 'QX WSA Succesfully Updated');
        return redirect()->route('qxwsa.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Qxwsa  $qxwsa
     * @return \Illuminate\Http\Response
     */
    public function show(Qxwsa $qxwsa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Qxwsa  $qxwsa
     * @return \Illuminate\Http\Response
     */
    public function edit(Qxwsa $qxwsa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Qxwsa  $qxwsa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Qxwsa $qxwsa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Qxwsa  $qxwsa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Qxwsa $qxwsa)
    {
        //
    }
}
