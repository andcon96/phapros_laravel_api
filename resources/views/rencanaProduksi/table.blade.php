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
        <?php $g = 0; ?>
        <?php $h = 0; ?>
        <!-- Estimasi Stock Terakhir -->
        @foreach ($periodeEstimasiStockAkhir as $key => $period)
            <!-- Ambil stock akhir bulan sebelumnya + rencana produksi - forecast -->
            @if ($loop->first)
                <?php $stockTerakhir = $item->item_stock_qty_oh; ?>
            @endif

            @if ($item->totalForecast->count() == 0)
                <?php $forecast = 0; ?>
            @else
                @if ($item->totalForecast->has($g) && $item->totalForecast[$g]->rp_mrp_bulan == strstr($key, "'", true))
                    <?php $forecast = $item->totalForecast[$g]->totalForecast; ?>
                    <?php $g++ ?>
                @else
                    <?php $forecast = 0; ?>
                @endif
            @endif

            @if ($item->totalRencanaProduksi->count() == 0)
                <?php $rencanaProduksi = 0; ?>
            @else
                @if ($item->totalRencanaProduksi->has($h) && $item->totalRencanaProduksi[$h]->rp_mrp_bulan == strstr($key, "'", true))
                    <?php $rencanaProduksi = $item->totalRencanaProduksi[$h]->totalRencanaProduksi; ?>
                    <?php $h++; ?>
                @else
                    <?php $rencanaProduksi = 0; ?>
                @endif
            @endif
            <?php $estimasi = $stockTerakhir + $rencanaProduksi - $forecast; ?>
            <?php $stockTerakhir = $estimasi; ?>

            <td class="text-center">{{ str_replace(',', '', number_format($estimasi, 2)) }}</td>
        @endforeach
        <!-- End Estimasi Stock Terakhir -->

        <?php $i = 0; ?>
        <!-- Forecast -->
        @foreach ($periodeRencanaProduksi as $key => $period)
            @if ($item->totalForecast->count() == 0)
                <td class="text-center">0</td>
            @else
                @if ($item->totalForecast->has($i) && $item->totalForecast[$i]->rp_mrp_bulan == strstr($key, "'", true))
                    <td class="text-center">{{ $item->totalForecast[$i]->totalForecast }}</td>
                <?php $i++ ?>
                @else
                    <td class="text-center">0</td>
                @endif
            @endif
        @endforeach
        <!-- End Forecast -->

        <?php $j = 0; ?>
        <!-- Rencana Produksi -->
        @foreach ($periodeRencanaProduksi as $key => $period)
            @if ($item->totalRencanaProduksi->count() == 0)
                <td class="text-center">0</td>
            @else
                @if ($item->totalRencanaProduksi->has($j) && $item->totalRencanaProduksi[$j]->rp_mrp_bulan == strstr($key, "'", true))
                    <td class="text-center nilaiRencanaProd" data-toggle="modal" data-target="#popUpRencanaProd"
                            data-itemcode="{{ $item->item_code }}"
                            data-bulan="{{ $item->totalRencanaProduksi[$j]->rp_mrp_bulan }}"
                            data-tahun="{{ $item->totalRencanaProduksi[$j]->rp_mrp_tahun }}" style="cursor:pointer;"
                            onmouseover="this.style.textDecoration='underline';"
                            onmouseout="this.style.textDecoration='none';">
                            {{ $item->totalRencanaProduksi[$j]->totalRencanaProduksi ?? 0 }}</td>

                <?php $j++ ?>
                @else
                    <td class="text-center">0</td>
                @endif
            @endif
        @endforeach
        <!-- End Rencana Produksi -->
    </tr>
@empty
    <tr>
        <td colspan="{{ $totalPeriode == 0 ? 11 : $totalPeriode * 2 + 8 }}" style="color:red">
            <center>No Data Available</center>
        </td>
    </tr>
@endforelse
