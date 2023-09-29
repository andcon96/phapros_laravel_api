@forelse ($data as $datas)
    <tr>
        <td data-title="Name">{{ $datas->eqa_rcpt_nbr }}</td>
        <td data-title="Username">{{ $datas->eqa_qxtend_message }}</td>
        <td data-title="Aksesweb">{{ $datas->created_at }}</td>
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
        {{ $data->withQueryString()->links() }}
    </td>
</tr>
