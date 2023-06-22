<?php

namespace App\Exports;

use DateInterval;
use DatePeriod;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class ExportKetersediaanRawMaterial implements FromView, ShouldAutoSize,WithEvents
{
    use RegistersEventListeners;
    

	public function __construct($itemcode,$datalen)
    {
        $this->itemcode 		= $itemcode;
        $this->datalength       = $datalen;
    }

    public function view(): View
    {   
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
            ->orderBy('t_year_duedate','asc')
            ->get();
            

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
            ->orderBy('t_year_duedate','asc')
            ->get();

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
            
            if($this->itemcode){
                $tablemaster = $tablemaster->where('t_mrp_part',$this->itemcode);
                
            }
            else{
                $tablemaster = $tablemaster;
            }
            
            return view('ketersediaanRawMaterial.exportToExcel', compact('tablemaster','dataPerMonthAndYear','datalen'));
        
    }
    public static function afterSheet(AfterSheet $event)
    {
        
        $datalength = ($event->getConcernable()->datalength*2) + 10;
        $datastart = Coordinate::stringFromColumnIndex(1);
        $dataend = Coordinate::stringFromColumnIndex($datalength);
        
        $sheet = $event->sheet->getDelegate();
        $sheet->getStyle($datastart.'1:'.$dataend.'2')->getBorders()->getAllBorders()
    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        
    }

    
}
