@extends('layout.layout')

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h4>Rencana Produksi {{ now()->year }}</h4>
                            <h5>{{ now()->format('F') }} {{ now()->year }} - Dec {{ now()->year }}</h5>
                        </div>
                        <br>
                        <form action="{{ route('rencanaProd.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-1 col-sm-1"></div>
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        <div class="col-md-3 col-sm-3 pt-2">
                                            <label for="itemCode"><b>Item Code: </b></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select id="s_itemcode" name="s_itemcode" hidden>
                                                <option value="">Select Item Code</option>
                                                @foreach ($allItems as $item)
                                                    <option value="{{ $item->item_code }}">{{ $item->item_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        <div class="col-md-3 col-sm-3 pt-2">
                                            <label for="itemDesc"><b>Item Desc: </b></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select id="s_itemdesc" name="s_itemdesc">
                                                <option value="">Select Description</option>
                                                @foreach ($allItems as $item)
                                                    <option value="{{ $item->item_description }}">
                                                        {{ $item->item_description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-1 col-sm-1"></div>
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        <div class="col-md-3 col-sm-3">
                                            <label for="kelompokProduksi"><b>Kelompok Produksi: </b></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select id="s_kelompokProduksi" name="s_kelompokProduksi">
                                                <option value="">Select Kel. Produksi</option>
                                                @foreach ($groupedProduksi as $item)
                                                    <option value="{{ $item->item_design_group }}">
                                                        {{ $item->item_design_group }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        <div class="col-md-3 col-sm-3 pt-2">
                                            <label for="makeTo"><b>Make To</b></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select id="s_makeTo" name="s_makeTo">
                                                <option value="">Select Make To Type</option>
                                                @foreach ($groupedMakeTo as $item)
                                                    <option value="{{ $item->item_make_to_type }}">
                                                        {{ $item->item_make_to_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-1 col-sm-1"></div>
                                <div class="col-md-5 col-sm-5"></div>
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group row">
                                        <div class="col-md-3 col-sm-3 pt-2"></div>
                                        <div class="col-md-6 col-sm-6">
                                            <button type="button" class="btn btn-outline-primary">Clear</button>
                                            &nbsp;
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1"></div>
                            </div>
                        </form>
                        <br>
                        <div class="table-responsive" style="width: 100%; overflow: auto;">
                            <table class="table table-bordered table-responsive-sm" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" rowspan="2">Kode Produk</th>
                                        <th class="text-center align-middle" rowspan="2">Kelompok Produksi</th>
                                        <th class="text-center align-middle" rowspan="2">Deskripsi</th>
                                        <th class="text-center align-middle" rowspan="2">Pareto {{ now()->year }} New
                                        </th>
                                        <th class="text-center align-middle" rowspan="2">MTS / MTO</th>
                                        <th class="text-center align-middle" rowspan="2">ED</th>
                                        <th class="text-center align-middle" rowspan="2">BV</th>
                                        <th class="text-center align-middle" rowspan="2">Qty</th>
                                        <th class="text-center align-middle" colspan="{{ $totalPeriode }}">Estimasi Stock
                                            Akhir</th>
                                        <th class="text-center align-middle" colspan="{{ $totalPeriode }}">Forecast</th>
                                        <th class="text-center align-middle" colspan="{{ $totalPeriode }}}">Rencana
                                            Produksi
                                        </th>
                                    </tr>
                                    <tr>
                                        @foreach ($periodeRencanaProduksi as $key => $period)
                                            <th>{{ $key }}</th>
                                        @endforeach
                                        @foreach ($periodeRencanaProduksi as $key => $period)
                                            <th>{{ $key }}</th>
                                        @endforeach
                                        @foreach ($periodeRencanaProduksi as $key => $period)
                                            <th>{{ $key }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @include('rencanaProduksi.table')
                                </tbody>
                            </table>
                        </div>
                        <br>
                        {!! $itemRencanaProduksi->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader" class="lds-dual-ring hidden overlay"></div>
@endsection

@section('scripts')
    <script>
        $('#s_itemcode').select2();
        $('#s_itemdesc').select2();
        $('#s_kelompokProduksi').select2();
        $('#s_makeTo').select2();
    </script>
@endsection
