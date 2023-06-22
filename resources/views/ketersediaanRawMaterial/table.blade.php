@forelse ($tablemaster as $key => $dm)

    <tr>
        
        <td class="text-center">{{ $dm->t_mrp_part }}</td>
        <td class="text-center">{{ $dm->t_pt_desc1 }}</td>
        <td class="text-center">{{ $dm->t_pt_um }}</td>
        <td class="text-center">{{ round($dm->t_ld_qty_oh,2) }}</td>
        <td class="text-center">{{round($dm->t_pod_qty_oh,2)}}</td>
        <td class="text-center">{{round($dm->t_rqm_qty,2)}}</td>
        <td class="text-center stockAkhir">{{round($dm->total,2)}}</td>
        
        {{-- {{dd($dm->arraybahan)}} --}}
         @foreach($dm->arraybahan as $ab)
            <td class="text-center arraybahan">{{$ab}}</td>
        @endforeach
        
        <td class="text-center totalbahan">{{round($dm->totalbahan,2)}}</td>
        @foreach($dm->arraystok as $as)
        <td class="text-center arraystok">{{$as}}</td>
    @endforeach
    </tr>
@empty
    <tr>
        <td colspan="{{ $datalen == 0 ? 10 : $datalen +1 }}"style="color:red">
            <center>No Data Available</center>
        </td>
    </tr>
@endforelse
