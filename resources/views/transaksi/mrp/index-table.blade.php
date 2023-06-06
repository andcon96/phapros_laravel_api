@forelse ($datamrp as $datamrps)
    <tr>
        <td data-title="Product Line">{{ $datamrps['t_pt_prod_line'] }}</td>
        <td data-title="Order Type">{{ $datamrps['t_mrp_type'] }}</td>
        <td data-title="Data Set">{{ $datamrps['t_mrp_dataset'] }}</td>
        <td data-title="Item No">{{ $datamrps['t_mrp_part'] }}</td>
        <td data-title="Description">{{ $datamrps['t_partdesc'] }}</td>
        <td data-title="Batch (Order)">{{ $datamrps['t_mrp_nbr'] }}</td>
        <td data-title="Quantity (Sched Rcpt)">{{ $datamrps['t_mrp_qty'] }}</td>
        <td data-title="Quantity On Hand">{{ $datamrps['t_ld_qty_oh'] }}</td>
        <td data-title="Status WO / PR">{{ $datamrps['t_pt_pm_code'] }}</td>
        <td data-title="Released Date">{{ $datamrps['t_mrp_rel_date'] }}</td>
        <td data-title="Due Date">{{ $datamrps['t_mrp_due_date'] }}</td>
        <td data-title="Action Message">{{ $datamrps['t_pt_ord_per'] }}</td>
        <td data-title="Order Period">{{ $datamrps['t_pt_ord_mult'] }}</td>
        <td data-title="Order Multiple">{{ $datamrps['t_pt_ord_min'] }}</td>
        <td data-title="Minimum Order">{{ $datamrps['t_oa_det'] }}</td>
    </tr>
@empty
    <tr>
        <td class="text-danger" colspan='15'>
            <center><b>No Data Available</b></center>
        </td>
    </tr>
@endforelse
<tr style="border:0 !important">
    <td colspan="12">
        {{ $datamrp->withQueryString()->links() }}
    </td>
</tr>
