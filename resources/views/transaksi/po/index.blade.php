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

                                <button type="button" class="btn btn-outline-secondary" data-toggle="modal"
                                    data-target="#exampleModalCenter"><span class="btn-icon-left text-secondary"><i
                                            class="fa fa-download color-secondary"></i>
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
                                        <th width="5%">Action</th>
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Excel</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" style="text-align:center;">
                        <button type="button" class="btn btn-outline-secondary" id="btnexport"
                            data-tipeexport="1"><span class="btn-icon-left text-secondary"><i
                                    class="fa fa-download color-secondary"></i>
                            </span>Download PO Detail</button>
                    </div>
                    <div class="col-md-12 mt-3" style="text-align: center;">
                        <button type="button" class="btn btn-outline-secondary" id="btnexport"
                            data-tipeexport="2"><span class="btn-icon-left text-secondary"><i
                                    class="fa fa-download color-secondary"></i>
                            </span>Download Approval Hist</button>
                    </div>
                    <div class="col-md-12 mt-3" style="text-align: center;">
                        <button type="button" class="btn btn-outline-secondary" id="btnexport"
                            data-tipeexport="3"><span class="btn-icon-left text-secondary"><i
                                    class="fa fa-download color-secondary"></i>
                            </span>Download Receipt Detail</button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- List Receipt Modal -->
    <div class="modal fade" id="exportModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History Receipt & Export PDF</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th width="75%">Receipt Number</th>
                                        <th width="25%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyreceipt">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export PDF Modal -->
    <div class="modal fade" id="modal2">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History Receipt & Export PDF</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" style="text-align:center;">
                        <h3 id="rcpnbr"></h3>
                        <input type="hidden" id="text_rcpnbr">
                    </div>
                    <div class="col-md-12" style="text-align:center;">
                        <button type="button" class="btn btn-outline-secondary btnexportpdf" data-tipeexport="1"><span
                                class="btn-icon-left text-secondary"><i class="fa fa-download color-secondary"></i>
                            </span>Download Checklist Penerimaan</button>
                    </div>
                    <div class="col-md-12 mt-3 blanko" style="text-align: center;">
                        <button type="button" class="btn btn-outline-secondary btnexportpdf" data-tipeexport="2"><span
                                class="btn-icon-left text-secondary"><i class="fa fa-download color-secondary"></i>
                            </span>Download Blanko Penyimpanan</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
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
            let tipeexport = $(this).data('tipeexport');
            let domain = $('#domain').val();
            let ponbr = $('#ponbr').val();
            let vendor = $('#vendor').val();
            let shipto = $('#shipto').val();
            let start_orddate = $('#start_orddate').val();
            let end_orddate = $('#end_orddate').val();

            let datarequest =
                "?domain=:domain&ponbr=:ponbr&vendor=:vendor&shipto=:shipto&start_orddate=:start_orddate&end_orddate=:end_orddate&tipe=:tipe";

            datarequest = datarequest.replace(':domain', domain);
            datarequest = datarequest.replace(':ponbr', ponbr);
            datarequest = datarequest.replace(':vendor', vendor);
            datarequest = datarequest.replace(':shipto', shipto);
            datarequest = datarequest.replace(':start_orddate', start_orddate);
            datarequest = datarequest.replace(':end_orddate', end_orddate);
            datarequest = datarequest.replace(':tipe', tipeexport);


            let url = "{{ route('ExportPO') }}" + datarequest;

            window.open(url, '_blank');
        });

        $(document).on('click', '.viewreceipt', function(e) {
            let ponbr = $(this).data('ponbr');
            let rcphist = $(this).data('rcpdetail');

            let output = '';
            $.each(rcphist, function(index, value) {
                let arr_rcp = value['get_detail_reject'];
                let arr_rcp_length = arr_rcp.length;

                output += '<tr>'
                output += '<td>' + value['rcpt_nbr'] + '</td>'
                output += '<td>'
                output +=
                    '<a href="#" class="detailreceipt ml-2 mr-2" data-rcpnbr="' +
                    value['rcpt_nbr'] + '""><i class="fa fa-eye color-secondary"></i></a> '
                output +=
                    '<a href="#" class="downloadpdf" data-toggle="modal" data-target="#modal2" data-rcpnbr="' +
                    value['rcpt_nbr'] + '"" data-hasreject="' + arr_rcp_length +
                    '"><i class="fa fa-download color-secondary"></i></a> '
                output += '</td>'
                output += '</tr>'
            });

            $('#bodyreceipt').html('').append(output);
        });

        $(document).on('click', '.downloadpdf', function(e) {
            let rcpnbr = $(this).data('rcpnbr');
            let hasreject = $(this).data('hasreject');

            hasreject == 0 ? $('.blanko').hide() : $('.blanko').show() ;

            $('#rcpnbr').html(rcpnbr);
            $('#text_rcpnbr').val(rcpnbr);
        })

        $(document).on('click', '.detailreceipt', function(e) {
            e.preventDefault();

            let rcpnbr = $(this).data('rcpnbr');

            let datarequest = "?rcpnbr=:rcpnbr";
            datarequest = datarequest.replace(':rcpnbr', rcpnbr);

            let url = "{{ route('viewReceipt') }}" + datarequest;

            window.open(url, '_blank');
        });

        $(document).on('click', '.btnexportpdf', function(e) {
            e.preventDefault();
            let tipeexport = $(this).data('tipeexport'); // 1 Checklist Penerimaan Barang , 2 Blanko Penyimpanan
            let rcpnbr = $('#text_rcpnbr').val();

            let datarequest = "?rcpnbr=:rcpnbr&tipe=:tipe";

            datarequest = datarequest.replace(':tipe', tipeexport);
            datarequest = datarequest.replace(':rcpnbr', rcpnbr);

            let url = "{{ route('ExportReceiptPDF') }}" + datarequest;

            window.open(url, '_blank');
        })
    </script>
@endsection
