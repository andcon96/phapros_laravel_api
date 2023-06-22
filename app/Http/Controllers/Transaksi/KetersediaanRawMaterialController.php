<?php

namespace App\Http\Controllers\Transaksi;

use App\Exports\ExportKetersediaanRawMaterial;
use App\Http\Controllers\Controller;
use App\Models\Transaksi\ItemMRP;
use App\Services\WSAServices;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class KetersediaanRawMaterialController extends Controller
{
    public function index(Request $request)
    {
        // $allItems = ItemMRP::get();
        ini_set('max_execution_time', '300'); 
        // $datamrp = (new WSAServices())->wsaRawMaterial();
        
        // create temp table
            // Schema::create('ketersediaan_temp', function($table)
            // {
            //     $table->string('t_mrp_part');
            //     $table->integer('t_mrp_duedate');
            //     $table->integer('t_year_duedate');
            //     $table->string('t_pt_desc1');
            //     $table->string('t_pt_desc2');
            //     $table->string('t_pt_um');
            //     $table->decimal('t_ld_qty_oh')->nullable();
            //     $table->decimal('t_pod_qty_oh')->nullable();
            //     $table->decimal('t_rqm_qty')->nullable();
            //     $table->decimal('t_qty_bahan')->nullable();
            //     $table->decimal('t_qty_stok')->nullable();
            //     $table->temporary();
            // });

            
            // DB::table('ketersediaan_temp')->truncate();
            // foreach(array_chunk($datamrp,500) as $t){
            //     DB::table('ketersediaan_temp')->insert($t);
            // }
            $tablemaster = DB::table('ketersediaan_temp')
            ->selectRaw('
            t_mrp_part,
            AVG(t_pt_um) as t_pt_um,
            t_pt_desc1,
            AVG(t_pt_um) as t_pt_um,
            AVG(t_ld_qty_oh) as t_ld_qty_oh,
            AVG(t_pod_qty_oh) as t_pod_qty_oh,
            AVG(t_rqm_qty) as t_rqm_qty,
            AVG(t_ld_qty_oh) +
            AVG(t_pod_qty_oh) +
            AVG(t_rqm_qty) as total,
            SUM(t_qty_bahan) as totalbahan
            ')
            ->groupBy('t_mrp_part')
            ->orderBy('t_mrp_part','asc')
            ->orderBy('t_mrp_duedate','asc')
            ->orderBy('t_year_duedate','asc');
            if($request->s_itemcode){
                $tablemaster = $tablemaster->where('t_mrp_part',$request->s_itemcode);
            }
            
            $tablemaster = $tablemaster->get();
            

            $tabledetail = DB::table('ketersediaan_temp')
            ->selectRaw('
            t_mrp_part,
            t_mrp_duedate,
            t_year_duedate,
            SUM(t_qty_bahan) as t_qty_bahan,
            SUM(t_qty_stok) as t_qty_stok
            ')
            ->groupBy(['t_mrp_part','t_year_duedate','t_mrp_duedate'])
            ->orderBy('t_mrp_part','asc')
            ->orderBy('t_mrp_duedate','asc')
            ->orderBy('t_year_duedate','asc');
            if($request->s_itemcode){
                $tabledetail = $tabledetail->where('t_mrp_part',$request->s_itemcode);
            }

            $tabledetail = $tabledetail->get();

            $maxdue= DB::table('ketersediaan_temp')->selectRaw('t_mrp_duedate,t_year_duedate')->orderBy('t_year_duedate','desc')->orderBy('t_mrp_duedate','desc')->first();
            $mindue= DB::table('ketersediaan_temp')->selectRaw('t_mrp_duedate,t_year_duedate')->orderBy('t_year_duedate','asc')->orderBy('t_mrp_duedate','asc')->first();

            $maxyear = $maxdue->t_year_duedate;
            $maxmonth = $maxdue->t_mrp_duedate;

            $minyear = $mindue->t_year_duedate;
            $minmonth = $mindue->t_mrp_duedate;

            $datalen = (($maxyear - $minyear) * 12) + ($maxmonth - $minmonth);

            $daterange = DB::table('ketersediaan_temp')->selectRaw('t_mrp_duedate,t_year_duedate')->groupBy(['t_year_duedate','t_mrp_duedate'])->orderBy('t_year_duedate','asc')->orderBy('t_mrp_duedate','asc')->get();
            $start    = (new DateTime($minyear.'-'.$minmonth.'-'.'01'))->modify('first day of this month');
            $end      = (new DateTime($maxyear.'-'.$maxmonth.'-'.'01'))->modify('first day of next month');
            
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            
            $arraytempdetail = [];
            $dataPerMonthAndYear = [];
            
            foreach ($period as $dt) {
                array_push($dataPerMonthAndYear,$dt->format("M-Y"));
                $arraytempbahan[(string)(int)($dt->format("m")).(string)(int)($dt->format("Y"))] = 0.00;
                $arraytempstok[(string)(int)($dt->format("m")).(string)(int)($dt->format("Y"))] = 0.00;
                
            }
            
            $curritem = '';
            $currkey = 0;
            
            foreach($tabledetail as $key => $td){
                
                if($curritem == ''){
                    $curritem = $td->t_mrp_part;
                    $currkey = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->keys()->first();
                    $arraytempbahan[$td->t_mrp_duedate.$td->t_year_duedate] = round(floatval($td->t_qty_bahan),2);
                    $tablemasterdata = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->first();
                    
                    $arraytempstok[$td->t_mrp_duedate.$td->t_year_duedate] = round(floatval($tablemasterdata->total + $tablemasterdata->totalbahan - $td->t_qty_stok),2);
                }
                else if($curritem == $td->t_mrp_part){
                    
                    $tablemasterdata = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->first();
                    $arraytempbahan[$td->t_mrp_duedate.$td->t_year_duedate] = round(floatval($td->t_qty_bahan),2);
                    $arraytempstok[$td->t_mrp_duedate.$td->t_year_duedate] = round(floatval($tablemasterdata->total + $tablemasterdata->totalbahan - $td->t_qty_stok),2);
                    if($key == count($tabledetail)-1){
                        $tablemasterdata = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->first();
                        $arraytempbahanreset = array_values($arraytempbahan);
                        $arraytempstokreset = array_values($arraytempstok);
                        
                        $tablemaster[$currkey]->arraybahan = $arraytempbahanreset;
                        $tablemaster[$currkey]->arraystok = $arraytempstokreset;
                    }
                }
                else{
                    
                    $tablemasterdata = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->first();
                    $arraytempbahanreset = array_values($arraytempbahan);
                    $arraytempstokreset = array_values($arraytempstok);
                    
                    $tablemaster[$currkey]->arraybahan = $arraytempbahanreset;
                    $tablemaster[$currkey]->arraystok = $arraytempstokreset;
                    
                    $keybahan = array_keys($arraytempbahan);
                    $arraytempbahan = array_fill_keys($keybahan, 0.00);

                    $keystok = array_keys($arraytempstok);
                    $arraytempstok = array_fill_keys($keystok, 0.00);
                    
                    //startnew
                    
                    $curritem = $td->t_mrp_part;
                    $currkey = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->keys()->first();
                    $arraytempbahan[$td->t_mrp_duedate.$td->t_year_duedate] = round(floatval($td->t_qty_bahan),2);
                    $tablemasterdata = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->first();
                    
                    $arraytempstok[$td->t_mrp_duedate.$td->t_year_duedate] = round(floatval($tablemasterdata->total + $tablemasterdata->totalbahan - $td->t_qty_stok),2);
                    if($key == count($tabledetail)-1){
                        $tablemasterdata = $tablemaster->where('t_mrp_part',$td->t_mrp_part)->first();
                        $arraytempbahanreset = array_values($arraytempbahan);
                        $arraytempstokreset = array_values($arraytempstok);
                        
                        $tablemaster[$currkey]->arraybahan = $arraytempbahanreset;
                        $tablemaster[$currkey]->arraystok = $arraytempstokreset;
                    }
                }
                
                
                
            }
            
            $tablemaster = $this->paginate($tablemaster);
            
            
            $allItems = DB::table('ketersediaan_temp')->groupBy('t_mrp_part')->selectRaw('t_mrp_part,t_pt_desc1')->get();
            

        return view('ketersediaanRawMaterial.index',compact('dataPerMonthAndYear','tablemaster','datalen','allItems'));
    }

    public function exportToExcel(Request $request){
        
        $itemcode = $request->s_itemcode;

        $maxdue= DB::table('ketersediaan_temp')->selectRaw('t_mrp_duedate,t_year_duedate')->orderBy('t_year_duedate','desc')->orderBy('t_mrp_duedate','desc')->first();
        $mindue= DB::table('ketersediaan_temp')->selectRaw('t_mrp_duedate,t_year_duedate')->orderBy('t_year_duedate','asc')->orderBy('t_mrp_duedate','asc')->first();

        $maxyear = $maxdue->t_year_duedate;
        $maxmonth = $maxdue->t_mrp_duedate;

        $minyear = $mindue->t_year_duedate;
        $minmonth = $mindue->t_mrp_duedate;

        $datalen = (($maxyear - $minyear) * 12) + ($maxmonth - $minmonth);
        
        return Excel::download(new ExportKetersediaanRawMaterial($itemcode,$datalen), 'Ketersediaan_Raw_Material.xlsx');
    }
    public function paginate($items, $perPage = 10, $page = null, $options = [['path'=>'/planner']])
    {
        // dd($page,$perPage);
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, ['path'=>url('KetersediaanRawMaterial')]);
        
    }
}
