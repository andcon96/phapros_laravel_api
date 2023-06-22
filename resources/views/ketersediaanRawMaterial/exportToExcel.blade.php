<table class="table table-bordered table-responsive-sm" style="width:100%;">
    <thead>
        <tr>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">Kode Barang</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">Nama Barang</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">UM</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">STOK</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">PO</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">PR</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">TOTAL</th>
            <th class="text-center align-middle" style="text-align:center;background-color:#acb9ca;border: 5px solid" colspan="{{ $datalen+1 }}">Total Quantity Bahan</th>
            <th class="text-center align-middle" rowspan="2" style="text-align:center;background-color:#acb9ca;border: 5px solid">TOTAL</th>
            <th class="text-center align-middle" style="text-align:center;background-color:#acb9ca;border: 5px solid" colspan="{{ $datalen+1 }}">Stok -/+ terhadap Rencana Produksi</th>
            
            
        </tr>
        <tr>
            @foreach ($dataPerMonthAndYear as $bulanTahun)
            
                <th style="white-space: nowrap;text-align:center;background-color:#acb9ca;border: 5px solid">{{$bulanTahun}}</th>
                
            @endforeach
            @foreach ($dataPerMonthAndYear as $bt)
            
                <th style="white-space: nowrap;text-align:center;background-color:#acb9ca;border: 5px solid">{{$bt}}</th>
            
            @endforeach



        </tr>
    </thead>
    <tbody>
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
</tbody>
</table>
