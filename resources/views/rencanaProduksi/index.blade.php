@extends('layout.layout')

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h4>Rencana Produksi</h4>
                            @if (!empty($firstYearAndMonth) && !empty($lastYearAndMonth))
                                <h5>{{$firstYearAndMonth}} - {{$lastYearAndMonth}}</h5>
                            @endif
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
                                            <a href="{{route('rencanaProd.index')}}" id="refreshButton" class="btn btn-outline-primary">Reset</a>
                                            &nbsp;
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1"></div>
                            </div>
                        </form>
                        <br>
                        <input type="hidden" class="hiddenTotalColumn" value="{{ $totalPeriode }}">
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
                                        @foreach ($dataPerMonthAndYear as $bulanTahun)
                                            <th style="white-space: nowrap">{{$bulanTahun}}</th>
                                        @endforeach

                                        @foreach ($dataPerMonthAndYear as $bulanTahun)
                                            <th style="white-space: nowrap">{{$bulanTahun}}</th>
                                        @endforeach

                                        @foreach ($dataPerMonthAndYear as $bulanTahun)
                                            <th style="white-space: nowrap">{{$bulanTahun}}</th>
                                        @endforeach

                                    </tr>
                                </thead>
                                <tbody>
                                    @include('rencanaProduksi.table')
                                </tbody>
                            </table>
                        </div>
                        <br>
                        @if ($itemRencanaProduksi->count() > 0)
                            {!! $itemRencanaProduksi->links() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader" class="lds-dual-ring hidden overlay"></div>

    <div class="modal fade" id="popUpRencanaProd">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Rencana Produksi</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="width: 100%; overflow: auto;">
                        <table class="table table-bordered table-responsive-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>No. WO</th>
                                    <th>Due Date</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyRencanaProduksi"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function calculateEstimasiStockAkhir() {
            let totalColumnEstimasi = $('.hiddenTotalColumn').val();
            let totalRow = document.getElementsByClassName('itemcode').length;
            // console.log(totalColumnEstimasi);
            if (totalRow > 0) {
                for (r = 0; r < totalRow; r++) {
                    let estimasiAkhir = 0;
                    let stockAkhir = parseFloat(document.getElementsByClassName('stockAkhir')[r].innerHTML.replaceAll('.', ''));
                    let itemcode = document.getElementsByClassName('itemcode')[r].innerHTML;
                    // console.log(itemcode);
                    // console.log(stockAkhir);
                    for (i = 0; i < totalColumnEstimasi; i++) {
                        let nilaiForecast = parseFloat(document.getElementsByClassName('kolomForecast'+itemcode)[i].innerHTML.replaceAll('.', ''));
                        let nilaiRencanaProduksi = parseFloat(document.getElementsByClassName('kolomRencanaProd'+itemcode)[i].innerHTML.replaceAll('.', ''));
                        if (i == 0) {
                            estimasiAkhir = estimasiAkhir + stockAkhir + nilaiRencanaProduksi - nilaiForecast;
                        } else {
                            estimasiAkhir = estimasiAkhir + nilaiRencanaProduksi - nilaiForecast;
                        }
                        // estimasiAkhir = Number(estimasiAkhir).toLocaleString('en-US');
                        // console.log(estimasiAkhir);
                        document.getElementsByClassName('kolomEstimasiStockAkhir'+itemcode)[i].innerHTML = Number(estimasiAkhir).toLocaleString('en-US').replaceAll(',', '.');
                    }
                }
            }
        }

        $('#s_itemcode').select2();
        $('#s_itemdesc').select2();
        $('#s_kelompokProduksi').select2();
        $('#s_makeTo').select2();

        calculateEstimasiStockAkhir();

        $('.nilaiRencanaProd').on('click', function() {
            let itemcode = $(this).data('itemcode');
            let bulan = $(this).data('bulan');
            let tahun = $(this).data('tahun');
            // console.log(itemcode);
            // console.log(bulan);
            // console.log(tahun);

            $('#popUpRencanaProd').show();

            $.ajax({
                type: "GET",
                url: "{{ route('getDetailRencanaProduksi') }}",
                data: {
                    itemcode: itemcode,
                    bulan: bulan,
                    tahun: tahun,
                },
                success: function(data) {
                    console.log(data);
                    $('#tableBodyRencanaProduksi').empty().append(data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });
    </script>
@endsection
