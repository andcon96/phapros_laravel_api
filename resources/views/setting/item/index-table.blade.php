@forelse ($item as $items)
  <tr>
    <td data-title="Name">{{ $items->item_code }}</td>
    <td data-title="Username">{{ $items->item_desc }}</td>
    <td data-title="Aksesweb">{{ $items->item_rn }}</td>
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
