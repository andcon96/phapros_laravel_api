<?php

namespace App\Http\Controllers;

use App\Models\Transaksi\ForecastRencanaProduksi;
use App\Models\Transaksi\ItemMRP;
use Carbon\Carbon;
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

        $allItems = ItemMRP::get();
        $groupedProduksi = ItemMRP::groupBy('item_design_group')->get('item_design_group');
        $groupedMakeTo = ItemMRP::groupBy('item_make_to_type')->get('item_make_to_type');

        $itemRencanaProduksi = ItemMRP::query()->with(['totalForecast', 'totalRencanaProduksi']);

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

        $groupedTahun = ForecastRencanaProduksi::groupBy('rp_mrp_tahun')->get('rp_mrp_tahun');
        // foreach ($groupedTahun as $tahun) {
        //     dump($tahun->rp_mrp_tahun);
        // }
        // dd('stop');

        $bulan = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        // $rencanaProduksi = [];
        $lastPeriodeForecast = ForecastRencanaProduksi::selectRaw('rp_mrp_tahun, rp_mrp_bulan, rp_mrp_due_date')
            ->whereRaw('isForecast = 1')
            ->orderByRaw('YEAR(rp_mrp_due_date) ASC, MONTH(rp_mrp_due_date)')
            ->get()
            ->groupBy(function ($q) {
                return substr($q->rp_mrp_bulan, 0, 3) . "'" . $q->rp_mrp_tahun;
            })->last()->first();

        // dd($lastPeriodeForecast);

        // $periodeForecast = $periodeForecast->where('isForecast', 1)->groupBy('rp_mrp_tahun', 'rp_mrp_bulan')->orderBy('rp_mrp_due_date')->get();
        // foreach ($periodeForecast as $key => $fc) {
        //     dump($key);
        // }

        $currentDate = Carbon::now();
        $currentMonth = substr($currentDate->monthName, 0, 3);
        // $currentMonth = 'Jun';
        $currentYear = $currentDate->year;

        // Buat hitung jumlah kolom yang diperlukan untuk bulan + tahun
        $totalColumn = 0;
        $dataPerMonthAndYear = [];
        $latestMonth = '';
        $dataItem = [];
        // dd($dataPerMonthAndYear);

        foreach ($itemRencanaProduksi as $item) {
            $startMonth = '';
            $dataItem[$item->item_code] = [
                'item_design_group' => $item->item_design_group,
                'item_description' => $item->item_description,
                'item_pareto' => $item->item_pareto,
                'item_make_to_type' => $item->item_make_to_type,
                'item_ed' => $item->item_ed,
                'item_bv' => $item->item_bv,
                'item_last_stock_date' => $item->item_last_stock_date,
                'item_stock_qty_oh' => $item->item_stock_qty_oh,
            ];
            foreach ($groupedTahun as $tahun) {
                foreach ($bulan as $bln) {
                    $currentTahunBln = $bln . " ' " . $tahun->rp_mrp_tahun;
                    $dataForecast = ForecastRencanaProduksi::query()->selectRaw('rp_mrp_tahun, rp_mrp_bulan, SUM(rp_mrp_qty) as totalQty')
                        ->where('id_rp_item', $item->id)->where('isForecast', 1)
                        ->where('rp_mrp_tahun', $tahun->rp_mrp_tahun)
                        ->where('rp_mrp_bulan', $bln)
                        ->first();

                    $dataRencanaProduksi = ForecastRencanaProduksi::query()->selectRaw('rp_mrp_tahun, rp_mrp_bulan, SUM(rp_mrp_qty) as totalQty')
                        ->where('id_rp_item', $item->id)
                        ->where('isForecast', 0)
                        ->where('rp_mrp_dataset', 'wo_mstr')
                        ->where('rp_mrp_tahun', $tahun->rp_mrp_tahun)
                        ->where('rp_mrp_bulan', $bln)
                        ->first();

                    if ($tahun->rp_mrp_tahun == $currentYear) {
                        if ($bln == $currentMonth) {
                            $startMonth = 'yes';
                            $totalColumn = $totalColumn + 1;
                            // array_push($)

                            if (!$dataForecast->totalQty) {
                                $dataItem[$item->item_code]['forecast'][] = [
                                    'tahun' => $tahun->rp_mrp_tahun,
                                    'bulan' => $bln,
                                    'totalQty' => 0
                                ];
                            } else {
                                $dataItem[$item->item_code]['forecast'][] = [
                                    'tahun' => $tahun->rp_mrp_tahun,
                                    'bulan' => $bln,
                                    'totalQty' => str_replace(',', '.', number_format($dataForecast->totalQty, 0),)
                                ];
                            }

                            if (!$dataRencanaProduksi->totalQty) {
                                $dataItem[$item->item_code]['rencanaProduksi'][] = [
                                    'tahun' => $tahun->rp_mrp_tahun,
                                    'bulan' => $bln,
                                    'totalQty' => 0
                                ];
                            } else {
                                $dataItem[$item->item_code]['rencanaProduksi'][] = [
                                    'tahun' => $tahun->rp_mrp_tahun,
                                    'bulan' => $bln,
                                    'totalQty' => str_replace(',', '.', number_format($dataRencanaProduksi->totalQty, 0),)
                                ];
                            }


                            if (!in_array($bln . "' " . $tahun->rp_mrp_tahun, $dataPerMonthAndYear)) {
                                $dataPerMonthAndYear[] = $bln . "' " . $tahun->rp_mrp_tahun;
                            }

                            if ($tahun->rp_mrp_tahun == $lastPeriodeForecast->rp_mrp_tahun && $bln == $lastPeriodeForecast->rp_mrp_bulan) {
                                break;
                            }
                        } else {
                            if ($startMonth == 'yes') {
                                $totalColumn = $totalColumn + 1;
                                if (!$dataForecast->totalQty) {
                                    $dataItem[$item->item_code]['forecast'][] = [
                                        'tahun' => $tahun->rp_mrp_tahun,
                                        'bulan' => $bln,
                                        'totalQty' => 0
                                    ];
                                } else {
                                    $dataItem[$item->item_code]['forecast'][] = [
                                        'tahun' => $tahun->rp_mrp_tahun,
                                        'bulan' => $bln,
                                        'totalQty' => str_replace(',', '.', number_format($dataForecast->totalQty, 0),)
                                    ];
                                }

                                if (!$dataRencanaProduksi->totalQty) {
                                    $dataItem[$item->item_code]['rencanaProduksi'][] = [
                                        'tahun' => $tahun->rp_mrp_tahun,
                                        'bulan' => $bln,
                                        'totalQty' => 0
                                    ];
                                } else {
                                    $dataItem[$item->item_code]['rencanaProduksi'][] = [
                                        'tahun' => $tahun->rp_mrp_tahun,
                                        'bulan' => $bln,
                                        'totalQty' => str_replace(',', '.', number_format($dataRencanaProduksi->totalQty, 0),)
                                    ];
                                }
                                if (!in_array($bln . "' " . $tahun->rp_mrp_tahun, $dataPerMonthAndYear)) {
                                    $dataPerMonthAndYear[] = $bln . "' " . $tahun->rp_mrp_tahun;
                                }

                                if ($tahun->rp_mrp_tahun == $lastPeriodeForecast->rp_mrp_tahun && $bln == $lastPeriodeForecast->rp_mrp_bulan) {
                                    break;
                                }
                            }
                        }
                    } else {
                        $totalColumn = $totalColumn + 1;
                        if (!$dataForecast->totalQty) {
                            $dataItem[$item->item_code]['forecast'][] = [
                                    'tahun' => $tahun->rp_mrp_tahun,
                                    'bulan' => $bln,
                                    'totalQty' => 0
                                ];
                        } else {
                            $dataItem[$item->item_code]['forecast'][] = [
                                'tahun' => $tahun->rp_mrp_tahun,
                                'bulan' => $bln,
                                'totalQty' => str_replace(',', '.', number_format($dataForecast->totalQty, 0),)
                            ];
                        }

                        if (!$dataRencanaProduksi->totalQty) {
                            $dataItem[$item->item_code]['rencanaProduksi'][] = [
                                'tahun' => $tahun->rp_mrp_tahun,
                                'bulan' => $bln,
                                'totalQty' => 0
                            ];
                        } else {
                            $dataItem[$item->item_code]['rencanaProduksi'][] = [
                                'tahun' => $tahun->rp_mrp_tahun,
                                'bulan' => $bln,
                                'totalQty' => str_replace(',', '.', number_format($dataRencanaProduksi->totalQty, 0),)
                            ];
                        }

                        if (!in_array($bln . "' " . $tahun->rp_mrp_tahun, $dataPerMonthAndYear)) {
                            $dataPerMonthAndYear[] = $bln . "' " . $tahun->rp_mrp_tahun;
                        }

                        if ($tahun->rp_mrp_tahun == $lastPeriodeForecast->rp_mrp_tahun && $bln == $lastPeriodeForecast->rp_mrp_bulan) {
                            break;
                        }
                    }
                }
            }
        }
        $firstYearAndMonth = $dataPerMonthAndYear[array_key_first($dataPerMonthAndYear)];
        $lastYearAndMonth = $dataPerMonthAndYear[array_key_last($dataPerMonthAndYear)];
        $totalPeriode = count($dataPerMonthAndYear);
        // dd($dataItem);
        // dd($totalPeriode);
        // dd($dataPerMonthAndYear);
        return view('rencanaProduksi.index', compact('itemRencanaProduksi', 'dataItem', 'groupedTahun', 
        'bulan', 'totalPeriode', 'allItems', 'dataPerMonthAndYear', 'firstYearAndMonth', 'lastYearAndMonth',
        'groupedProduksi', 'groupedMakeTo', 'currentYear', 'currentMonth', 'totalColumn'));
    }

    public function getDetailRencanaProduksi(Request $request)
    {
        $itemcode = $request->itemcode;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $output = '';

        $dataItem = ItemMRP::where('item_code', $itemcode)->first();
        if ($dataItem) {
            $dataDetail = ForecastRencanaProduksi::where('id_rp_item', $dataItem->id)
                ->where('isForecast', 0)
                ->where('rp_mrp_type', '!=', 'demand')
                ->where('rp_mrp_tahun', $tahun)
                ->where('rp_mrp_bulan', $bulan)
                ->get();

            foreach ($dataDetail as $detail) {
                $output .= '<tr>';
                $output .= '<td>' . $detail->rp_mrp_nbr . '</td>';
                $output .= '<td>' . $detail->rp_mrp_due_date . '</td>';
                $output .= '<td>' . $detail->rp_mrp_qty . '</td>';
                $output .= '</tr>';
            }
        }

        return $output;
    }
}
