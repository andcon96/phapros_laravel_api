@forelse ($item as $items)
    <tr>
        <td data-title="Name">{{ $items->item_code }}</td>
        <td data-title="Username">{{ $items->item_desc }}</td>
        <td data-title="Aksesweb">{{ $items->item_rn }}</td>
        <td data-title="Action">
            <a href="#" data-toggle="modal" data-target="#editModal" class="editmodal"
                data-itemcode="{{ $items->item_code }}" data-itemdesc="{{ $items->item_desc }}"
                data-id="{{ $items->id }}" data-itemrn="{{ $items->item_rn }}">
                <i class="fa fa-edit"></i></a>
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
        {{ $item->withQueryString()->links() }}
    </td>
</tr>
