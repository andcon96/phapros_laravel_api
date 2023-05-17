@extends('layout.layout')

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form class="form-horizontal" method="get" action="{{ route('purchaseorder.index') }}">
                    <div class="form-group row col-md-12">


                        <div class="form-group row col-md-12">
                            <label for="domain" class="col-md-2 col-form-label text-md-right">{{ __('Domain') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <input id="domain" type="text" class="form-control" name="domain" autocomplete="off"
                                    value="">
                            </div>
                            <label for="ponbr"
                                class="col-md-2 col-form-label text-md-right">{{ __('PO Number') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <select id="ponbr" class="form-control select2data" name="ponbr" autofocus
                                    autocomplete="off">
                                    <option value=""> Select Data </option>
                                    @foreach ($listpo as $listpos)
                                        <option value="{{ $listpos->po_nbr }}">{{ $listpos->po_nbr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row col-md-12">
                            <label for="vendor" class="col-md-2 col-form-label text-md-right">{{ __('Vendor') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <select id="vendor" class="form-control select2data" name="vendor" autofocus
                                    autocomplete="off">
                                    <option value=""> Select Data </option>
                                    @foreach ($listvend as $listvends)
                                        <option value="{{ $listvends->po_vend }}">{{ $listvends->po_vend }} --
                                            {{ $listvends->po_vend_desc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="shipto" class="col-md-2 col-form-label text-md-right">{{ __('Ship To') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <select id="shipto" class="form-control select2data" name="shipto" autofocus
                                    autocomplete="off">
                                    <option value=""> Select Data </option>
                                    @foreach ($listshipto as $listshiptos)
                                        <option value="{{ $listshiptos->po_ship }}">{{ $listshiptos->po_ship }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row col-md-12">
                            <label for="start_orddate"
                                class="col-md-2 col-form-label text-md-right">{{ __('Start Order Date') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <input id="start_orddate" type="text" class="form-control datepicker-default"
                                    name="start_orddate" autocomplete="off" value="">
                            </div>
                            <label for="end_orddate"
                                class="col-md-2 col-form-label text-md-right">{{ __('End Order Date') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <input id="end_orddate" type="text" class="form-control datepicker-default"
                                    name="end_orddate" autocomplete="off" value="">
                            </div>
                        </div>

                        <div class="form-group row col-md-12">
                            <label for="start_orddate"
                                class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
                            <div class="col-md-4 col-lg-3">
                                <button class="btn btn-outline-secondary" class="btn bt-ref" id="btnsearch"><span
                                        class="btn-icon-left text-secondary"><i class="fa fa-search color-secondary"></i>
                                    </span>Search</button>
                                <button type="button" class="btn btn-outline-secondary" id="btnexport"><span
                                        class="btn-icon-left text-secondary"><i class="fa fa-download color-secondary"></i>
                                    </span>Download</button>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th width="12%">Domain</th>
                                        <th width="12%">PO Number</th>
                                        <th width="25%">Vendor</th>
                                        <th width="12%">Ship To</th>
                                        <th width="12%">PO Order Date</th>
                                        <th width="12%">PO Due Date</th>
                                        <th width="12%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @include('transaksi.po.index-table')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="loader" class="lds-dual-ring hidden overlay"></div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('.select2data').select2();

        $(document).ready(function() {
            var cur_url = window.location.href;

            let paramString = cur_url.split('?')[1];
            let queryString = new URLSearchParams(paramString);

            let domain = queryString.get('domain');
            let ponbr = queryString.get('ponbr');
            let vendor = queryString.get('vendor');
            let shipto = queryString.get('shipto');
            let start_orddate = queryString.get('start_orddate');
            let end_orddate = queryString.get('end_orddate');

            $('#domain').val(domain);
            $('#ponbr').val(ponbr).trigger('change');
            $('#vendor').val(vendor).trigger('change');
            $('#shipto').val(shipto).trigger('change');
            $('#start_orddate').val(start_orddate);
            $('#end_orddate').val(end_orddate);
        });
        
        $(document).on('click', '#btnexport', function(e) {
            e.preventDefault();
            let domain = $('#domain').val();
            let ponbr = $('#ponbr').val();
            let vendor = $('#vendor').val();
            let shipto = $('#shipto').val();
            let start_orddate = $('#start_orddate').val();
            let end_orddate = $('#end_orddate').val();

            let datarequest  = "?domain=:domain&ponbr=:ponbr&vendor=:vendor&shipto=:shipto&start_orddate=:start_orddate&end_orddate=:end_orddate"; 
            
            datarequest = datarequest.replace(':domain', domain);
            datarequest = datarequest.replace(':ponbr', ponbr);
            datarequest = datarequest.replace(':vendor', vendor);
            datarequest = datarequest.replace(':shipto', shipto);
            datarequest = datarequest.replace(':start_orddate', start_orddate);
            datarequest = datarequest.replace(':end_orddate', end_orddate);
            
            
            let url = "{{ route('ExportPO') }}" + datarequest;

            window.open(url, '_blank');
        })
    </script>
@endsection
