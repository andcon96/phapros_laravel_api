@extends('layout.layout')

@section('content')
    <input type="hidden" id="tmp_username" />
    <input type="hidden" id="tmp_name" />

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                

                <form class="form-horizontal" method="get" action="{{ route('errorlist.index') }}">
                    <div class="form-group row col-md-12">
                        <label for="listitem" class="col-md-2 col-form-label text-md-right">{{ __('No Receipt') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <select id="listitem" class="form-control select2data" name="listitem" autofocus
                                autocomplete="off">
                                <option value=""> Select Data </option>
                                @foreach ($listerror as $listitems)
                                    <option value="{{ $listitems->eqa_rcpt_nbr }}">{{ $listitems->eqa_rcpt_nbr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="listitem" class="col-md-2 col-form-label text-md-right">{{ __('Tanggal') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input type="date" class="form-control" id="tanggalkejadian" name="tanggalkejadian">
                        </div>
                        
                        {{-- <label for="end_orddate" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label> --}}
                        <button class="btn btn-outline-secondary ml-2" class="btn bt-ref" id="btnload">Search</button>
                        <button class="btn btn-outline-secondary ml-2" class="btn bt-ref" id="btnrefresh"><i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <div class="row">
                    <div class="offset-lg-1 col-lg-10">

                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th width="12%">No Receipt</th>
                                        <th width="24%">Pesan Error QAD</th>
                                        <th width="12%">Tanggal Terjadi</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @include('setting.errorqxtend.index-table')
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
        $('#btnrefresh').on('click',function(){
            $('#listitem').val(''); 
            $('#tanggalkejadian').val(''); 
        })
        
        $(document).ready(function() {
            var cur_url = window.location.href;

            let paramString = cur_url.split('?')[1];
            let queryString = new URLSearchParams(paramString);

            let listitem = queryString.get('listitem');

            $('#listitem').val(listitem).trigger('change');
        });
    </script>
@endsection
