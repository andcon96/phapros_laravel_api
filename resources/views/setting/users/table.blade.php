@forelse ($userweblist as $show)
    <tr>
        <td data-title="Name">{{ $show->name }}</td>
        <td data-title="Username">{{ $show->username }}</td>
        <td data-title="Aksesweb">{{ $show->can_access_web == 1 ? 'Yes' : 'No' }}</td>
        <td data-title="Receivemail">{{ $show->can_receive_email == 1 ? 'Yes' : 'No' }}</td>
        <td data-title="Approver">{{ $show->user_approver == 1 ? 'Yes' : 'No' }}</td>
        <td data-title="accessmenuit">{{ $show->can_access_it_menu == 1 ? 'Yes' : 'No' }}</td>
        <td data-title="active">{{ $show->is_active == 1 ? 'Yes' : 'No' }}</td>

        <td data-title="Edit" class="action">
            <a href="" class="editUser mt-3" data-id="{{ $show->id }}" data-uname="{{ $show->username }}"
                data-accessweb="{{ $show->can_access_web }}" data-approver="{{ $show->user_approver }}"
                data-receivemail="{{ $show->can_receive_email }}" data-accessmenuit="{{ $show->can_access_it_menu }}"
                data-toggle='modal' data-target="#editModal">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                    height="24px" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path
                            d="M10.5,8 L6,19 C6.0352633,19.1332661 6.06268417,19.2312688 6.08226261,19.2940083 C6.43717645,20.4313361 8.07642225,21 9,21 C10.5,21 11,19 12.5,19 C14,19 14.5917308,20.9843119 16,21 C16.9388462,21.0104588 17.9388462,20.3437921 19,19 L14.5,8 L10.5,8 Z"
                            fill="#000000" />
                        <path d="M11.3,6 L12.5,3 L13.7,6 L11.3,6 Z M14.5,8 L10.5,8 L14.5,8 Z" fill="#000000" />
                    </g>
                </svg>
            </a>
        </td>

        {{-- <td data-title="Pass" class="action">
      @if ($show->can_access_web == 1)
      <a href="" class="changepass" data-id="{{$show->id}}" data-uname="{{$show->username}}" data-toggle='modal' data-target="#changepassModal"><i class="fas fa-key"></i></a>
      @endif
    </td> --}}
        <td data-title="Delete" class="action">

            <a href="" class="deleteUser" data-id="{{ $show->id }}" data-username="{{ $show->username }}"
                data-name="{{ $show->name }}" data-status="{{ $show->is_active }}" data-toggle='modal'
                data-target="#deleteModal"><svg xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                    version="1.1" class="svg-main-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <path
                            d="M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z M21,8 L17,8 C16.4477153,8 16,7.55228475 16,7 C16,6.44771525 16.4477153,6 17,6 L21,6 C21.5522847,6 22,6.44771525 22,7 C22,7.55228475 21.5522847,8 21,8 Z"
                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                        <path
                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                            fill="#000000" fill-rule="nonzero" />
                    </g>
                </svg></a>

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
