@forelse ($itemRencanaProduksi as $item)
    <tr>
        <td class="text-center">{{ $item->item_code }}</td>
        <td class="text-center">{{ $item->item_design_group }}</td>
        <td class="text-center">{{ $item->item_description }}</td>
        <td class="text-center">{{ $item->item_pareto }}</td>
        <td class="text-center">{{ $item->item_make_to_type }}</td>
        <td class="text-center">{{ $item->item_ed }}</td>
        <td class="text-center">{{ $item->item_bv }}</td>
        <td class="text-center">{{ $item->item_stock_qty_oh }}</td>
        <?php $h = 0; ?>
        <!-- Estimasi Stock Terakhir -->
        @foreach ($periodeEstimasiStockAkhir as $key => $period)
        <!-- Ambil stock akhir bulan sebelumnya + rencana produksi - forecast -->
        @if ($key == 0)
            <?php $stockTerakhir = $item->item_stock_qty_oh ?>
        @endif

        @if ($item->totalForecast->count() == 0)
            <?php $forecast = 0; ?>
        @else
            @if ($item->totalForecast->has($h))
                <?php $forecast = $item->totalForecast[$h]->totalForecast; ?>
            @else
                <?php $forecast = 0; ?>
            @endif
        @endif

        @if ($item->totalRencanaProduksi->count() == 0)
            <?php $rencanaProduksi = 0; ?>
        @else
            @if ($item->totalRencanaProduksi->has($h))
                <?php $rencanaProduksi = $item->totalRencanaProduksi[$h]->totalRencanaProduksi; ?>
            @else
                <?php $rencanaProduksi = 0; ?>
            @endif
        @endif
        <?php $estimasi = $stockTerakhir + $rencanaProduksi - $forecast; ?>
        <?php $stockTerakhir = $estimasi; ?>

        <td class="text-center">{{str_replace(',', '', number_format($estimasi, 2))}}</td>
        <?php $h++ ?>
        @endforeach
        <!-- End Estimasi Stock Terakhir -->

        <?php $i = 0; ?>
        <!-- Forecast -->
        @foreach ($periodeRencanaProduksi as $period)
            @if ($item->totalForecast->count() == 0)
                <td class="text-center">0</td>
            @else
                @if ($item->totalForecast->has($i))
                    <td class="text-center">{{$item->totalForecast[$i]->totalForecast}}</td>
                @else
                    <td class="text-center">0</td>
                @endif
            @endif
            <?php $i++ ?>
        @endforeach
        <!-- End Forecast -->

        <?php $j = 0 ?>
        <!-- Rencana Produksi -->
        @foreach ($periodeRencanaProduksi as $period)
            @if ($item->totalRencanaProduksi->count() == 0)
                <td class="text-center">0</td>
            @else
                @if ($item->totalRencanaProduksi->has($j))
                    <td class="text-center">{{$item->totalRencanaProduksi[$j]->totalRencanaProduksi ?? 0}}</td>
                @else
                    <td class="text-center">0</td>
                @endif
            @endif
            <?php $j++ ?>
        @endforeach
        <!-- End Rencana Produksi -->
    </tr>
@empty
    <tr>
        <td colspan="{{$totalPeriode * 2 + 8}}" style="color:red">
            <center>No Data Available</center>
        </td>
    </tr>
@endforelse
