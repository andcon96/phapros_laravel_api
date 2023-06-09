<?php

namespace App\Exports;

use App\Services\WSAServices;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportMRP implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct($productline,$s_itemcode)
    {
        $this->productline      = $productline;
        $this->s_itemcode       = $s_itemcode;
    }

    public function collection()
    {
        $productline    = $this->productline;
        $s_itemcode     = $this->s_itemcode;

        $datamrp = (new WSAServices())->wsamrp();
        
        if($productline){
            $datamrp = $datamrp->where('t_pt_prod_line','=',$productline);
        }
        
        if($s_itemcode){
            $datamrp = $datamrp->where('t_mrp_part','=',$s_itemcode);
        }

        return $datamrp;
    }

    public function map($datamrp): array
    {
        return [
            $datamrp['t_pt_prod_line'],
            $datamrp['t_mrp_type'],
            $datamrp['t_mrp_dataset'],
            $datamrp['t_mrp_part'],
            $datamrp['t_partdesc'],
            $datamrp['t_mrp_nbr'],
            $datamrp['t_mrp_qty'],
            $datamrp['t_ld_qty_oh'],
            $datamrp['t_pt_pm_code'],
            $datamrp['t_mrp_rel_date'],
            $datamrp['t_mrp_due_date'],
            $datamrp['t_pt_ord_per'],
            $datamrp['t_pt_ord_mult'],
            $datamrp['t_pt_ord_min'],
            $datamrp['t_oa_det'],
        ];
    }
    public function headings(): array
    {
        return [
            'PROD LINE',
            'ORDER TYPE',
            'DATA SET',
            'ITEM NO',
            'BATCH (ORDER)',
            'QUANTITY (SCHED RCPT)',
            'QUANTITY ON HAND',
            'STATUS WO/ PR',
            'RELEASED DATE',
            'DUE DATE',
            'ACTION MESSAGE',
            'ORDER PERIOD',
            'ORDER MULTIPLE',
            'MINIMUM ORDER',
        ];
    }
}
