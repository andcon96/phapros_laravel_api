@extends('layout.layout')

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h4>MRP Browse</h4>
                        </div>
                        <br>
                        <form action="{{ route('menumrp.index') }}" method="GET">
                            <div class="row">
                                <div class="offset-md-1 col-md-10 col-sm-10">
                                    <div class="form-group row">
                                        <div class="col-md-2 col-sm-2 pt-2">
                                            <label for="itemCode"><b>Product Line: </b></label>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <input type="text" class="form-control" name="productline" id="productline">
                                        </div>
                                        <div class="col-md-2 col-sm-2 pt-2">
                                            <label for="itemCode"><b>Item Code: </b></label>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <select id="s_itemcode" name="s_itemcode" hidden>
                                                <option value="">Select Item Code</option>
                                                @foreach ($allItems as $item)
                                                    <option value="{{ $item->item_code }}">{{ $item->item_code }} -- {{ $item->item_description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-md-1 col-md-10 col-sm-10">
                                    <div class="form-group row">
                                        <div class="offset-md-2 col-md-6 col-sm-6">
                                            <a href="{{route('menumrp.index')}}" id="refreshButton" class="btn btn-outline-primary">Reset</a>
                                            &nbsp;
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1"></div>
                            </div>
                        </form>

                        
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped" style="white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th width="12%">Prod Line</th>
                                        <th width="12%">Order Type</th>
                                        <th width="25%">Data Set</th>
                                        <th width="12%">item No</th>
                                        <th width="12%">Description</th>
                                        <th width="12%">Batch (Order)</th>
                                        <th width="12%">Quantity (Sched Rcpt)</th>
                                        <th width="12%">Quantity On Hand</th>
                                        <th width="12%">Status WO/ PR</th>
                                        <th width="12%">Released Date</th>
                                        <th width="12%">Due Date</th>
                                        <th width="12%">Action Message</th>
                                        <th width="12%">Order Period</th>
                                        <th width="12%">Order Multiple</th>
                                        <th width="12%">Minimum Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @include('transaksi.mrp.index-table')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader" class="lds-dual-ring hidden overlay"></div>

@endsection

@section('scripts')
    <script>
        $('#s_itemcode').select2({width: '100%'});

        
        $(document).ready(function() {
            var cur_url = window.location.href;

            let paramString = cur_url.split('?')[1];
            let queryString = new URLSearchParams(paramString);

            let productline = queryString.get('productline');
            let s_itemcode = queryString.get('s_itemcode');

            $('#productline').val(productline);
            $('#s_itemcode').val(s_itemcode).trigger('change');
        });
    </script>
@endsection
