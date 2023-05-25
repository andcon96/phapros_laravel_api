@extends('layout.layout')

@section('content')
    <input type="hidden" id="tmp_username" />
    <input type="hidden" id="tmp_name" />

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="col-md-2 mt-2">
                        <form action="{{ route('LoadItem') }}" method="POST" class="form-gorizontal">
                            {{ method_field('POST') }}
                            {{ csrf_field() }}
                            <button class="btn btn-outline-secondary ml-2" class="btn bt-ref" id="btnload">Load
                                Item</button>

                        </form>
                    </div>
                </div>

                <form class="form-horizontal" method="get" action="{{ route('itemmaint.index') }}">
                    <div class="form-group row col-md-12">
                        <label for="listitem" class="col-md-2 col-form-label text-md-right">{{ __('Item') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <select id="listitem" class="form-control select2data" name="listitem" autofocus
                                autocomplete="off">
                                <option value=""> Select Data </option>
                                @foreach ($listitem as $listitems)
                                    <option value="{{ $listitems->item_code }}">{{ $listitems->item_code }} -- {{ $listitems->item_desc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="end_orddate" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
                        <button class="btn btn-outline-secondary ml-2" class="btn bt-ref" id="btnload">Search</button>
                    </div>
                </form>

                <div class="row">
                    <div class="offset-lg-1 col-lg-10">

                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th width="12%">Item Code</th>
                                        <th width="24%">Item Desc</th>
                                        <th width="12%">Running Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @include('setting.item.index-table')
                                </tbody>
                                <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                                <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
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
        $('#listitem').select2({
            width: '100%'
        });
        
        $(document).ready(function() {
            var cur_url = window.location.href;

            let paramString = cur_url.split('?')[1];
            let queryString = new URLSearchParams(paramString);

            let listitem = queryString.get('listitem');

            $('#listitem').val(listitem).trigger('change');
        });
    </script>
@endsection
