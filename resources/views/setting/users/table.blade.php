@forelse ($userweblist as $show)
  <tr>
    <td data-title="Name">{{ $show->name }}</td>
    <td data-title="Username">{{ $show->username }}</td>
    <td data-title="Aksesweb">{{ $show->can_access_web == 1 ? 'Yes' : 'No' }}</td>

    <td data-title="Edit" class="action">
      <a href="" class="editUser" data-id="{{$show->id}}" data-uname="{{$show->username}}" data-accessweb="{{$show->can_access_web}}" data-toggle='modal' data-target="#editModal"><i class="fas fa-edit"></i></a>
    </td>

    {{-- <td data-title="Pass" class="action">
      @if($show->can_access_web == 1)
      <a href="" class="changepass" data-id="{{$show->id}}" data-uname="{{$show->username}}" data-toggle='modal' data-target="#changepassModal"><i class="fas fa-key"></i></a>
      @endif
    </td> --}}
    <td data-title="Delete" class="action">
      
      <a href="" class="deleteUser" data-id="{{$show->id}}" data-username="{{$show->username}}" data-name="{{$show->name}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash"></i></a>
       
    </td> 
  </tr>
@empty
<tr>
  <td class="text-danger" colspan='12'>
    <center><b>No Data Available</b></center>
  </td>
</tr>
@endforelse
<tr style="border:0 !important">
  <td colspan="12">
    {{ $userweblist->withQueryString()->links() }}
  </td>
</tr>             
