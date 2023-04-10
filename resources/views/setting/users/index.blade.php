@extends('layout.layout')

@section('menu_name','Users Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
  <li class="breadcrumb-item active">User Web Maintenance</li>
</ol>
@endsection

@section('content')

<input type="hidden" id="tmp_username" />
<input type="hidden" id="tmp_name" />
<form class="form-horizontal" method="get" action="{{route('usermaint.index')}}">
<div class="form-group row col-md-12">
  
    {{ method_field('get') }}
    {{ csrf_field() }}

  <div class="col-md-2 mt-2">
    <input type="button" class="btn bt-action newUser mb-3" data-toggle="modal" data-target="#createModal" value="Create User">
      
  </div>
  <label for="s_username" class="col-md-1 mt-2 col-form-label">{{ __('Username') }}</label>
  <div class="col-md-2 mt-2">
    <input id="s_username" type="text" class="form-control" name="s_username" autocomplete="off" autofocus>
  </div>

  <label for="s_name" class="col-md-1 mt-2 col-form-label">{{ __('Name') }}</label>
  <div class="col-md-2 mt-2">
    <input id="s_name" type="text" class="form-control" name="s_name" autocomplete="off" autofocus>
  </div>

  <div class="col-md-2 offset-md-1 mt-2">
    <button class="btn bt-action ml-2" class="btn bt-ref" id="btnsearch" >Search</button>
    <button class="btn bt-action ml-2" id='btnrefresh' style="width: 40px !important" type="submit"><i class="fa fa-sync"></i></button>
  </div>
  
</div>
</form>
<div class="table-responsive tag-container col-lg-12 col-md-12">
  <table class="table table-bordered" id="dataTable" width="50%" cellspacing="0">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Username</th>
        <th width="7%">Akses Web</th>
        <th width="7%">Receive Email</th>
        <th width="7%">Approver</th>
        <th width="7%">Edit</th>
        
        <th width="7%">Delete</th>
      </tr>
    </thead>
    <tbody>
      @include('setting.users.table')
    </tbody>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
  </table>

</div>

<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Web User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" id="formcreate" method="post" action="{{route('usermaint.store')}}">
        {{ method_field('post') }}
        {{ csrf_field() }}

        <div class="modal-body">
          
          <div class="form-group row">
            <label for="username" class="col-md-3 col-form-label text-md-right">Kode User</label>
            <div class="col-md-5 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <select name="username" id="c_username" class="form-control" required>
                <option value="">Select Data</option>
                @foreach ($userkaryawanlist as $karyawan)
                  <option value="{{$karyawan->nik}}">{{$karyawan->nik}} -- {{$karyawan->nama}}</option>
                @endforeach
              </select>
              {{-- <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" autocomplete="off" required autofocus> --}}
            </div>
          </div>
          <div class="form-group row" style="display: none">
            <label for="name" class="col-md-3 col-form-label text-md-right">Nama User</label>
            <div class="col-md-5">
              <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off" autofocus required readonly>
            </div>
          </div>

          <div class="form-group row">
            <label for="checkreceiveemail" class="col-md-3 col-form-label text-md-right">Receive Email</label>
            <div class="col-md-5 mt-auto mb-auto">
              <input type="checkbox" name="checkreceivemail" id="checkreceivemail" style="size: 20px">
            </div>
          </div>

          <div class="form-group row">
            <label for="checkapprover" class="col-md-3 col-form-label text-md-right">Approver</label>
            <div class="col-md-5 mt-auto mb-auto">
              <input type="checkbox" name="checkapprover" id="checkapprover" style="size: 20px">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="accessweb" class="col-md-3 col-form-label text-md-right">Access Web</label>
            <div class="col-md-5 mt-auto mb-auto">
              <input type="checkbox" name="checkaccessweb" id="checkaccessweb" style="size: 20px">
            </div>
          </div>

          <div class="form-group row" id="rowpass" style="display: none">
            <label for="password" class="col-md-3 col-form-label text-md-right">Password</label>
            <div class="col-md-5">
              <input id="password" type="password" class="form-control" name="password" >
            </div>
          </div>
          <div class="form-group row" id="rowpassconf" style="display: none">
            <label for="password-confirm" class="col-md-3 col-form-label text-md-right">Confirm Password</label>
            <div class="col-md-5">
              <input id="password-confirm" type="password" class="form-control" name="password_confirmation" >
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="c_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id="c_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="c_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- edit modal --}}

<div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" id="formedit" method="post" action="{{route('usermaint.update', 'Edit')}}">
        {{ method_field('put') }}
        {{ csrf_field() }}

        <div class="modal-body">
          
          <div class="form-group row">
            <label for="username" class="col-md-3 col-form-label text-md-right">Kode User</label>
            <div class="col-md-5 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input type="text" name="username" id="e_username" class="form-control" required readonly>
                
              {{-- <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" autocomplete="off" required autofocus> --}}
            </div>
          </div>
          <div class="form-group row" style="display: none">
            <label for="name" class="col-md-3 col-form-label text-md-right">Nama User</label>
            <div class="col-md-5">
              <input id="e_name" type="text" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off" autofocus required readonly>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="e_checkreceivemail" class="col-md-3 col-form-label text-md-right">Receive Email</label>
            <div class="col-md-5 mt-auto mb-auto">
              <input type="checkbox" name="e_checkreceivemail" id="e_checkreceivemail" style="size: 20px">
            </div>
          </div>

          <div class="form-group row">
            <label for="e_checkapprover" class="col-md-3 col-form-label text-md-right">Approver</label>
            <div class="col-md-5 mt-auto mb-auto">
              <input type="checkbox" name="e_checkapprover" id="e_checkapprover" style="size: 20px">
            </div>
          </div>

          <div class="form-group row">
            <label for="e_accessweb" class="col-md-3 col-form-label text-md-right">Access Web</label>
            <div class="col-md-5 mt-auto mb-auto">
              <input type="checkbox" name="checkaccessweb" id="e_checkaccessweb" style="size: 20px">
              
            </div>
          </div>

          <div class="form-group row" id="e_rowpass" style="display: none">
            <label for="e_password" class="col-md-3 col-form-label text-md-right">Password</label>
            <div class="col-md-5">
              <input id="e_password" type="password" class="form-control" name="password" >
            </div>
          </div>
          <div class="form-group row" id="e_rowpassconf" style="display: none">
            <label for="e_password-confirm" class="col-md-3 col-form-label text-md-right">Confirm Password</label>
            <div class="col-md-5">
              <input id="e_password-confirm" type="password" class="form-control" name="password_confirmation" >
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- delete modal --}}

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Status User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{route('usermaint.destroy', 'Delete')}}" id="formdelete" method="post">
        @method('delete')
        {{ csrf_field() }}

        <div class="modal-body">

          <input type="hidden" name="temp_id" id="temp_id" value="">
          <input type="hidden" name="temp_active" id="temp_active">

          <div class="container">
            <div class="row">
              Are you sure you want to Delete user :&nbsp; <b><a name="temp_uname" id="temp_uname"></a></b> &nbsp;?
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger bt-action" id="d_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
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
  $('#c_username').select2({
    width: '100%'
  });
  
  $(document).on('change','#c_username',function(){
    if($('#c_username').val() != ''){
      var username = $('#c_username').find(":selected").text();
      var name = username.split(' -- ');
      
      $('#name').val(name[1]);
    }
  });

  $(document).on('change','#checkaccessweb',function(){
    
    if(document.getElementById('checkaccessweb').checked == true){
      document.getElementById('rowpass').style.display = '';
      document.getElementById('rowpassconf').style.display = '';
      document.getElementById('password').required = true;
      document.getElementById('password-confirm').required = true;
      
    }
    else{
      document.getElementById('rowpass').style.display = 'none';
      document.getElementById('rowpassconf').style.display = 'none';
      document.getElementById('password').required = false;
      document.getElementById('password-confirm').required = false;
    }
  });

  $(document).on('change','#e_checkaccessweb',function(){
    
    if(document.getElementById('e_checkaccessweb').checked == true){
      document.getElementById('e_rowpass').style.display = '';
      document.getElementById('e_rowpassconf').style.display = '';
      // document.getElementById('e_password').required = true;
      // document.getElementById('e_password-confirm').required = true;
      
    }
    else{
      document.getElementById('e_rowpass').style.display = 'none';
      document.getElementById('e_rowpassconf').style.display = 'none';
      // document.getElementById('e_password').required = false;
      // document.getElementById('e_password-confirm').required = false;
    }
  });

  $(document).on('click','.editUser',function(){
    var username = $(this).data('uname');
    var accessweb = $(this).data('accessweb');
    var receivemail = $(this).data('receivemail');
    var approver = $(this).data('approver');
    
    document.getElementById('e_username').value = username;
    if(accessweb == 1){
      document.getElementById('e_checkaccessweb').checked = true;
    }
    else{
      document.getElementById('e_checkaccessweb').checked = false;
      
    }
    if(receivemail == 1){
      document.getElementById('e_checkreceivemail').checked = true;
    }
    else{
      document.getElementById('e_checkreceivemail').checked = false;
      
    }
    if(approver == 1){
      document.getElementById('e_checkapprover').checked = true;
    }
    else{
      document.getElementById('e_checkapprover').checked = false;
      
    }
 

    $('#e_checkaccessweb').trigger("change");

  });

  $(document).on('submit','#formcreate',function(){
    $('#c_btnloading').show();
    $('#c_btnconf').hide();
    $('#c_btnclose').hide()
  })
  $(document).on('submit','#formdelete',function(){
    $('#d_btnloading').show();
    $('#d_btnconf').hide();
    $('#d_btnclose').hide()
  })
  $(document).on('submit','#formedit',function(){
    $('#e_btnloading').show();
    $('#e_btnconf').hide();
    $('#e_btnclose').hide()
  })



  $(document).on('click', '.deleteUser', function() { // Click to only happen on announce links

    //alert('tst');
    var uid = $(this).data('id');
    var username = $(this).data('username');
    var name = $(this).data('name');
    document.getElementById("temp_id").value = uid;
    document.getElementById("temp_uname").innerHTML = username + ' -- '+ name;
    
   

  });


</script>

@endsection