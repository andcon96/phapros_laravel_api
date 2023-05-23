<?php

namespace App\Http\Controllers;

use App\Models\Transaksi\ForecastRencanaProduksi;
use App\Models\Transaksi\ItemMRP;
use Illuminate\Http\Request;

class RencanaProduksiController extends Controller
{
    public function index(Request $request)
    {
        // $itemRencanaProduksi = ForecastRencanaProduksi::with(['getItemMRP'])
        //     ->whereBetween('rp_mrp_due_date', ['2023-01-01', '2023-12-30'])
        //     ->orderBy('rp_mrp_due_date', 'asc')
        //     ->get()
        //     ->groupBy(['rp_mrp_tahun', 'rp_mrp_bulan']);
        $itemRencanaProduksi = ItemMRP::query()->with(['totalForecast']);

        if ($request->s_itemcode) {
            $itemcode = $request->s_itemcode;
            $itemRencanaProduksi = $itemRencanaProduksi->where('item_code', $itemcode);
        }

        if ($request->s_itemdesc) {
            $itemdesc = $request->s_itemdesc;
            $itemRencanaProduksi = $itemRencanaProduksi->where('item_description', $itemdesc);
        }

        if ($request->s_kelompokProduksi) {
            $kelompokProduksi = $request->s_kelompokProduksi;
            $itemRencanaProduksi = $itemRencanaProduksi->where('item_design_group', $kelompokProduksi);
        }

        if ($request->s_makeTo) {
            $makeTo = $request->s_makeTo;
            $itemRencanaProduksi = $itemRencanaProduksi->where('item_make_to_type', $makeTo);
        }

        $itemRencanaProduksi = $itemRencanaProduksi->orderBy('item_code')->paginate(10);
        // $rencanaProduksi = [];
        $periodeRencanaProduksi = ForecastRencanaProduksi::selectRaw('rp_mrp_tahun, rp_mrp_bulan, rp_mrp_due_date')
            ->whereRaw('isForecast = 0')
            ->orderByRaw('YEAR(rp_mrp_due_date) ASC, MONTH(rp_mrp_due_date)')
            ->get()
            ->groupBy(function ($q) {
                return $q->rp_mrp_bulan . "'" . $q->rp_mrp_tahun;
            });

        $periodeEstimasiStockAkhir = $periodeRencanaProduksi->values()->keyBy(function ($items, $key) {
            return (int) $key;
        });

        $totalPeriode = $periodeRencanaProduksi->count();

        $allItems = ItemMRP::get();
        $groupedProduksi = ItemMRP::groupBy('item_design_group')->get('item_design_group');
        $groupedMakeTo = ItemMRP::groupBy('item_make_to_type')->get('item_make_to_type');

        // dd($periodeEstimasiStockAkhir);
        return view('rencanaProduksi.index', compact('itemRencanaProduksi', 'totalPeriode', 'periodeRencanaProduksi', 'periodeEstimasiStockAkhir', 'allItems', 'groupedProduksi', 'groupedMakeTo'));
    }
}
