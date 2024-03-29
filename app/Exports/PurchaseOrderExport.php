<?php

namespace App\Exports;

use App\Models\Transaksi\PurchaseOrderMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseOrderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private $flagpo = '';

    public function __construct($domain,$ponbr,$vendor,$shipto,$start_orddate,$end_orddate)
    {
        $this->domain           = $domain;
        $this->ponbr            = $ponbr;
        $this->vendor           = $vendor;
        $this->shipto           = $shipto;
        $this->start_orddate    = $start_orddate;
        $this->end_orddate      = $end_orddate;
    }

    public function collection()
    {
        $domain    = $this->domain;
        $ponbr    = $this->ponbr;
        $vendor    = $this->vendor;
        $shipto    = $this->shipto;
        $start_orddate    = $this->start_orddate;
        $end_orddate    = $this->end_orddate;

        $po = PurchaseOrderMaster::query()
                    ->join('pod_det','po_mstr.id','=','pod_det.pod_po_id');
                    // ->with(['getDetail', 'getHistoryReceipt']);

        if($domain){
            $po->where('po_domain',$domain);
        }
        if($ponbr){
            $po->where('po_nbr',$ponbr);
        }
        if($vendor){
            $po->where('po_vend',$vendor);
        }
        if($shipto){
            $po->where('po_ship',$shipto);
        }
        if($start_orddate){
            $po->where('po_ord_date','>=',$start_orddate);
        }
        if($end_orddate){
            $po->where('po_ord_date','<=',$end_orddate);
        }

        $po = $po->get();

        return $po;
    }

    public function map($po): array
    {
        if($this->flagpo == ''){
            $this->flagpo = $po->po_nbr;
            return [
                $po->po_domain,
                $po->po_nbr,
                $po->po_vend.' - '.$po->po_vend_desc,
                $po->po_ship,
                $po->po_ord_date,
                $po->po_due_date,
                $po->created_at,
                $po->pod_line,
                $po->pod_part.' - '.$po->pod_desc,
                $po->pod_qty_ord,
                $po->pod_qty_rcvd,
            ];
        }else{
            if($this->flagpo == $po->po_nbr){
                $this->flagpo = $po->po_nbr;
                return [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $po->pod_line,
                    $po->pod_part.' - '.$po->pod_desc,
                    $po->pod_qty_ord,
                    $po->pod_qty_rcvd,
                ];
            }else{
                $this->flagpo = $po->po_nbr;
                return [
                    $po->po_domain,
                    $po->po_nbr,
                    $po->po_vend.' - '.$po->po_vend_desc,
                    $po->po_ship,
                    $po->po_ord_date,
                    $po->po_due_date,
                    $po->created_at,
                    $po->pod_line,
                    $po->pod_part.' - '.$po->pod_desc,
                    $po->pod_qty_ord,
                    $po->pod_qty_rcvd,
                ];
            }
        }

    }

    public function headings(): array
    {
        return [
            'PO Domain',
            'PO Number',
            'PO Vendor',
            'PO Ship To',
            'PO Order Date',
            'PO Due Date',
            'Created At',
            'Line',
            'Part',
            'Qty Order',
            'Qty Received'
        ];
    }
}
