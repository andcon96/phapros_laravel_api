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
                                        <th width="6%">Action</th>
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


    {{-- Modal Edit --}}
    <div class="modal fade" id="editModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item RN</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>

                <form class="form-horizontal" id="formedit" method="post"
                action="{{ route('updateRunningNbrItem') }}">
                    {{ method_field('post') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <input type="hidden" id="e_id" name="e_id">
                            <div class="form-group row">
                                <label for="e_item" class="col-md-4 col-form-label text-md-right">Item</label>
                                <div class="col-md-8 {{ $errors->has('uname') ? 'has-error' : '' }}"><input
                                        id="e_item" type="text" class="form-control" name="e_item"
                                        value="{{ old('e_item') }}" autocomplete="off" required autofocus readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="e_yearrn" class="col-md-4 col-form-label text-md-right">Year (2 Digit)</label>
                                <div class="col-md-5 {{ $errors->has('uname') ? 'has-error' : '' }}"><input
                                        id="e_yearrn" minlength="2" maxlength="2" type="text"
                                        class="form-control" name="e_yearrn" value="{{ old('e_yearrn') }}"
                                        autocomplete="off" required autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="e_rn" class="col-md-4 col-form-label text-md-right">Running Number</label>
                                <div class="col-md-5 {{ $errors->has('uname') ? 'has-error' : '' }}"><input
                                        id="e_rn" type="text" class="form-control" name="e_rn"
                                        value="{{ old('e_rn') }}" minlength="4" maxlength="4" autocomplete="off"
                                        required autofocus>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success bt-action" id="c_btnconf">Save</button>
                        <button type="button" class="btn bt-action" id="c_btnloading" style="display:none">
                            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                        </button>
                    </div>
                </form>
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
        
        $('.editmodal').on('click', function() {
            let id = $(this).data('id');
            let itemcode = $(this).data('itemcode');
            let itemdesc = $(this).data('itemdesc');
            let rn = $(this).data('itemrn');

            let year = String(rn).substring(0, 2);
            let newrn = String(rn).substring(2);

            $('#e_rn').val(newrn);
            $('#e_yearrn').val(year);
            $('#e_item').val(itemcode + ' - ' + itemdesc);
            $('#e_id').val(id);
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
