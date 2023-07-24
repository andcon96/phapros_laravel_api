@extends('layout.layout')

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Master</a></li>
        <li class="breadcrumb-item active">Prefix Control</li>
    </ol>
@endsection

@section('content')
    <div class="table-responsive col-lg-12 col-md-12">
        <form action="{{ route('prefixmaint.store') }}" method="post" id="submit">
            {{ method_field('post') }}
            {{ csrf_field() }}

            <div class="row">
                <div class="offset-lg-1 col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="prefixrcpt"
                                    class="col-md-3 col-form-label text-md-right">{{ __('Prefix Receipt') }}</label>
                                <div class="col-md-2">
                                    <input id="prefixrcpt" type="text" class="form-control" name="prefixrcpt" autocomplete="off"
                                        value="{{ $prefix->prefix_rcpt_pr ?? '' }}" maxlength="3" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rnrcpt"
                                    class="col-md-3 col-form-label text-md-right">{{ __('Running Nbr Receipt') }}</label>
                                <div class="col-md-3">
                                    <input id="rnrcpt" type="text" class="form-control" autocomplete="off" name="rnrcpt"
                                        value="{{ $prefix->prefix_rcpt_rn ?? '' }}" maxlength="6" required>
                                    <span id="errorcur" style="color:red"></span>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="offset-lg-1 col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="prefixtidaksesuai"
                                    class="col-md-3 col-form-label text-md-right">{{ __('Prefix Ketidaksesuaian') }}</label>
                                <div class="col-md-2">
                                    <input id="prefixtidaksesuai" type="text" class="form-control" name="prefixtidaksesuai" autocomplete="off"
                                        value="{{ $prefix->prefix_ketidaksesuaian ?? '' }}" maxlength="3" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rntidaksesuai"
                                    class="col-md-3 col-form-label text-md-right">{{ __('Running Nbr Ketidaksesuaian') }}</label>
                                <div class="col-md-3">
                                    <input id="rntidaksesuai" type="text" class="form-control" autocomplete="off" name="rntidaksesuai"
                                        value="{{ $prefix->prefix_ketidaksesuaian_rn ?? '' }}" maxlength="6" required>
                                    <span id="errorcur" style="color:red"></span>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="offset-lg-1 col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 table-striped">
                                    <thead>
                                        <tr>
                                            <th>Prefix IMR</th>
                                            <th>Running Number</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyprefiximr">
                                        @foreach ($prefiximr as $prefiximrs)
                                            <tr class="btn-reveal-trigger">
                                                <td class="py-2"><input type="text" class="form-control pr" autocomplete="off" name="prefixedit[]"  style="height:37px;background-color:#d4d7da" value="{{ $prefiximrs->pin_prefix }}" disabled></td>
                                                <td class="py-2"><input type="number" oninput="this.value=this.value.slice(0,this.maxLength)" class="form-control rn" style="height:37px;background-color:#d4d7da" name="rnedit[]" min="1" step="1" maxlength="6" value="{{ $prefiximrs->pin_rn }}" disabled></td>
                                                <td data-title="Action"><button type="button" class="ibtnEdit btn btn-info btn-focus" ><i class="fa fa-pencil"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                <input type="button" class="btn btn-lg btn-block btn-focus" id="addrow"
                                                    value="Add Row" style="background-color:#1234A5; color:white; font-size:16px" />
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offset-lg-1 col-lg-10">
            <div class="row float-right">
                    <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
                    <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                        <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('#submit').on("submit", function() {
            document.getElementById('btnconf').style.display = 'none';
            document.getElementById('btnloading').style.display = '';
        });

        $("#addrow").on('click', function() {
            console.log('1');
            var newRow = $("<tr>");
            var cols = "";

            cols +=
                '<td data-title="prefix[]" data-label="Prefix"><input type="text" class="form-control" autocomplete="off" name="prefix[]" style="height:37px" required/></td>';

            cols +=
                '<td data-title="rn[]" data-label="Running Number"><input type="text" class="form-control rn" autocomplete="off" name="rn[]" value="000000" style="height:37px" min="1" step="1" required readonly/></td>';

            cols +=
                '<td data-title="Action"><button class="ibtnDel btn btn-danger btn-focus" ><i class="fa fa-trash"></i></button></td>';
            cols += '</tr>'
            newRow.append(cols);
            $("#bodyprefiximr").append(newRow);
        });

        $("table").on("click", ".ibtnDel", function(event) {
            $(this).closest("tr").remove();
            counter -= 1
        });

        $("table").on("click", ".ibtnEdit", function(event) {
            $(this).closest("tr").find("td").find(".rn").prop('disabled', false);
            $(this).closest("tr").find("td").find(".rn").css("background-color", "#ffffff");
            $(this).closest("tr").find("td").find(".pr").prop('disabled', false);
            $(this).closest("tr").find("td").find(".pr").prop('readonly', true);
            
        });
    </script>
@endsection
