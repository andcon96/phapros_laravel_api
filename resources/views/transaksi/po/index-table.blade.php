@forelse ($pomstr as $pomstrs)
    <tr>
        <td data-title="Domain">{{ $pomstrs->po_domain }}</td>
        <td data-title="PO Number">{{ $pomstrs->po_nbr }}</td>
        <td data-title="Vendor">{{ $pomstrs->po_vend }} -- {{ $pomstrs->po_vend_desc }}</td>
        <td data-title="Ship To">{{ $pomstrs->po_ship }}</td>
        <td data-title="Order Date">{{ $pomstrs->po_ord_date }}</td>
        <td data-title="Due Date">{{ $pomstrs->po_due_date }}</td>
        <td data-title="Status">{{ $pomstrs->po_status }}</td>
        <td data-title="Export">
            <a href="#" data-toggle="modal" data-target="#exportModal" class="viewreceipt"
                data-ponbr="{{ $pomstrs->po_nbr }}" data-rcpdetail="{{ $pomstrs->getHistoryReceipt }}">
                <i class="fa fa-file-pdf-o" style="color: red;"></i>
            </a>
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
        {{ $pomstr->withQueryString()->links() }}
    </td>
</tr>
