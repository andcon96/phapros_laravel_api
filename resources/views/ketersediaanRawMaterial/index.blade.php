@extends('layout.layout')

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h4>Ketersediaan Raw Material</h4>
                            @if (!empty($firstYearAndMonth) && !empty($lastYearAndMonth))
                                <h5>{{$firstYearAndMonth}} - {{$lastYearAndMonth}}</h5>
                            @endif
                        </div>
                        <br>
                        <form action="" id="formaction" method="GET">
                            <div class="row">
                                <div class="col-md-2 col-sm-2"></div>
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        <div class="col-md-3 col-sm-3 pt-2">
                                            <label for="itemCode"><b>Item Code: </b></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select id="s_itemcode" name="s_itemcode" hidden>
                                                <option value="">Select Item Code</option>
                                                @foreach ($allItems as $item)
                                                    <option value="{{ $item->t_mrp_part }}">{{$item->t_mrp_part}} -- {{ $item->t_pt_desc1 }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        
                                        <div class="col-md-6 col-sm-6">
                                            <a href="{{route('KetersediaanRawMaterial.index')}}" id="refreshButton" class="btn btn-outline-primary">Reset</a>
                                            &nbsp;
                                            <button type="button" id="btnsubmit" class="btn btn-primary">Search</button>
                                            &nbsp;
                                            <button type="button" id="exportButton" class="btn btn-outline-primary">Export To Excel</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1"></div>
                            </div>
                        </form>
                        <br>
                        {{-- <input type="hidden" class="hiddenTotalColumn" value="{{ $totalPeriode }}"> --}}
                        <div class="table-responsive" style="width: 100%; overflow: auto;">
                            <table class="table table-bordered table-responsive-sm" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" rowspan="2">Kode Barang</th>
                                        <th class="text-center align-middle" rowspan="2">Nama Barang</th>
                                        <th class="text-center align-middle" rowspan="2">UM</th>
                                        <th class="text-center align-middle" rowspan="2">STOK
                                        </th>
                                        <th class="text-center align-middle" rowspan="2">PO</th>
                                        <th class="text-center align-middle" rowspan="2">PR</th>
                                        <th class="text-center align-middle" rowspan="2">TOTAL</th>
                                        <th class="text-center align-middle" colspan="{{ $datalen+1 }}">Total Quantity Bahan</th>
                                        <th class="text-center align-middle" rowspan="2">TOTAL</th>
                                        <th class="text-center align-middle" colspan="{{ $datalen+1 }}">Stok -/+ terhadap Rencana Produksi</th>
                                        
                                        
                                    </tr>
                                    <tr>
                                        @foreach ($dataPerMonthAndYear as $bulanTahun)
                                        
                                            <th style="white-space: nowrap">{{$bulanTahun}}</th>
                                            
                                        @endforeach
                                        @foreach ($dataPerMonthAndYear as $bt)
                                        
                                            <th style="white-space: nowrap">{{$bt}}</th>
                                        
                                        @endforeach



                                    </tr>
                                </thead>
                                <tbody>
                                    @include('ketersediaanRawMaterial.table')
                                </tbody>
                            </table>
                        </div>
                        <br>
                        @if ($tablemaster->count() > 0)
                            {!! $tablemaster->links() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader" class="lds-dual-ring hidden overlay"></div>


@endsection

@section('scripts')
    <script type="text/javascript">
        $('#s_itemcode').select2();
        $(document).on('click','#btnsubmit',function(e){
            $('#formaction').attr('action',"{{ route('KetersediaanRawMaterial.index') }}").submit();
        });
        $(document).on('click','#exportButton',function(e){
            $('#formaction').attr('action',"{{route('exportToExcel')}}").submit();
        });

    </script>
@endsection
