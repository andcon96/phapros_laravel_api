@forelse ($dataItem as $key => $item)
    <tr>
        <td class="text-center itemcode">{{ $key }}</td>
        <td class="text-center">{{ $item['item_design_group'] }}</td>
        <td class="text-center">{{ $item['item_description'] }}</td>
        <td class="text-center">{{ $item['item_pareto'] }}</td>
        <td class="text-center">{{ $item['item_make_to_type'] }}</td>
        <td class="text-center">{{ str_replace(',', '.', number_format($item['item_ed'], 0)) }}</td>
        <td class="text-center">{{ str_replace(',', '.', number_format($item['item_bv'], 0)) }}</td>
        <td class="text-center stockAkhir">{{ str_replace(',', '.', number_format($item['item_stock_qty_oh'], 0)) }}</td>

        @foreach ($dataPerMonthAndYear as $bulanTahun)
            <td class="text-center kolomEstimasiStockAkhir<?= $key ?>">0</td>
        @endforeach

        @foreach ($item['forecast'] as $forecast)
            <td class="text-center kolomForecast<?= $key ?>">{{ $forecast['totalQty'] }}</td>
        @endforeach

        @foreach ($item['rencanaProduksi'] as $rencanaProduksi)
            <td class="text-center nilaiRencanaProd kolomRencanaProd<?= $key ?>"
                style="cursor: pointer;text-decoration:none;"
                onmouseover="this.style.textDecoration='{{ $rencanaProduksi == 0 ? '' : 'underline' }}';"
                onmouseout="this.style.textDecoration='none';" data-itemcode="{{ $key }}"
                data-bulan="{{ $rencanaProduksi['bulan'] }}" data-tahun="{{ $rencanaProduksi['tahun'] }}"
                data-toggle="modal" data-target="#popUpRencanaProd">
                {{ $rencanaProduksi['totalQty'] }}</td>
        @endforeach

    </tr>
@empty
    <tr>
        <td colspan="{{ $totalPeriode == 0 ? 11 : $totalPeriode * 2 + 8 }}"style="color:red">
            <center>No Data Available</center>
        </td>
    </tr>
@endforelse
