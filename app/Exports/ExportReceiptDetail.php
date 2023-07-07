<?php

namespace App\Exports;

use App\Models\Transaksi\PurchaseOrderMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportReceiptDetail implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    private $flagpo = '';
    private $rcptnbr = '';

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
                    // ->with([
                    //     'getHistoryReceipt.getDetail',
                    //     'getHistoryReceipt.getUser',
                    //     'getHistoryReceipt.getChecklist',
                    //     'getHistoryReceipt.getDocument',
                    //     'getHistoryReceipt.getKemasan',
                    //     'getHistoryReceipt.getTransport'
                    // ])
                    ->join('rcpt_mstr','po_mstr.id','rcpt_mstr.rcpt_po_id')
                    ->join('users','rcpt_mstr.rcpt_user_id','users.id')
                    ->join('rcptd_det','rcpt_mstr.id','rcptd_det.rcptd_rcpt_id')
                    ->join('rcpt_checklist','rcpt_mstr.id','rcpt_checklist.rcptc_rcpt_id')
                    ->join('rcpt_document','rcpt_mstr.id','rcpt_document.rcptdoc_rcpt_id')
                    ->join('rcpt_kemasan','rcpt_mstr.id','rcpt_kemasan.rcptk_rcpt_id')
                    ->join('rcpt_transport','rcpt_mstr.id','rcpt_transport.rcptt_rcpt_id');
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
            $this->rcptnbr = $po->rcpt_nbr;
            return [
                $po->po_domain,
                $po->po_nbr,
                $po->po_vend.' - '.$po->po_vend_desc,
                $po->po_ship,
                $po->po_ord_date,
                $po->po_due_date,
                $po->created_at,
                // Receipt Master
                $po->rcpt_nbr,
                $po->rcpt_status,
                $po->name,
                // Receipt Checklist
                $po->rcptc_imr_nbr,
                $po->rcptc_article_nbr,
                $po->rcptc_imr_date,
                $po->rcptc_arrival_date,
                $po->rcptc_prod_date,
                $po->rcptc_exp_date,
                $po->rcptc_manufacturer,
                $po->rcptc_country,
                // Receipt Document
                $po->rcptdoc_is_certofanalys == 1 ? 'Yes' : 'No',
                $po->rcptdoc_certofanalys,
                $po->rcptdoc_is_msds == 1 ? 'Yes' : 'No',
                $po->rcptdoc_msds,
                $po->rcptdoc_is_forwarderdo == 1 ? 'Yes' : 'No',
                $po->rcptdoc_forwarderdo,
                $po->rcptdoc_is_packinglist == 1 ? 'Yes' : 'No',
                $po->rcptdoc_packinglist,
                $po->rcptdoc_is_otherdocs == 1 ? 'Yes' : 'No',
                $po->rcptdoc_otherdocs,
                // Receipt Kemasan
                $po->rcptk_kemasan_sacdos == 1 ? 'Damage' : 'Not Damage',
                $po->rcptk_kemasan_sacdos_desc,
                $po->rcptk_kemasan_drumvat == 1 ? 'Damage' : 'Not Damage',
                $po->rcptk_kemasan_drumvat_desc,
                $po->rcptk_kemasan_palletpeti == 1 ? 'Damage' : 'Not Damage',
                $po->rcptk_kemasan_palletpeti_desc,
                $po->rcptk_is_clean == 1 ? 'Yes' : 'No',
                $po->rcptk_is_clean_desc,
                $po->rcptk_is_dry == 1 ? 'Yes' : 'No',
                $po->rcptk_is_dry_desc,
                $po->rcptk_is_not_spilled == 1 ? 'Yes' : 'No',
                $po->rcptk_is_not_spilled_desc,
                $po->rcptk_is_sealed == 1 ? 'Yes' : 'No',
                $po->rcptk_is_manufacturer_label,
                // Receipt Transport
                $po->rcptt_transporter_no,
                $po->rcptt_police_no,
                $po->rcptt_is_clean == 1 ? 'Yes' : 'No',
                $po->rcptt_is_clean_desc,
                $po->rcptt_is_dry == 1 ? 'Yes' : 'No',
                $po->rcptt_is_dry_desc,
                $po->rcptt_is_not_spilled == 1 ? 'Yes' : 'No',
                $po->rcptt_is_not_spilled_desc,
                $po->rcptt_is_position_single == 1 ? 'Single' : 'Kombinasi',
                $po->rcptt_is_position_single_desc,
                $po->rcptt_is_segregated == 1 ? 'Yes' : 'No',
                $po->rcptt_is_segregated_desc,
                // Receipt Catatan
                $po->rcptt_kelembapan,
                $po->rcptt_suhu,
                $po->rcptt_angkutan_catatan,
                // Receipt Detail
                $po->rcptd_line,
                $po->rcptd_part.' - '.$po->rcptd_part_desc,
                $po->rcptd_part_um,
                $po->rcptd_qty_per_package,
                $po->rcptd_qty_arr,
                $po->rcptd_qty_appr,
                $po->rcptd_qty_rej,
                $po->rcptd_loc,
                $po->rcptd_lot,
                $po->rcptd_batch,
            ];
        }else{
            if($this->flagpo == $po->po_nbr && $this->rcptnbr == $po->rcpt_nbr){
                $this->flagpo = $po->po_nbr;
                $this->rcptnbr = $po->rcpt_nbr;
                return [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    // Receipt Master
                    '',
                    '',
                    '',
                    // Receipt Checklist
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    // Receipt Document
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    // Receipt Kemasan
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    // Receipt Transport
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    // Receipt Catatan
                    '',
                    '',
                    '',
                    // Receipt Detail
                    $po->rcptd_line,
                    $po->rcptd_part.' - '.$po->rcptd_part_desc,
                    $po->rcptd_part_um,
                    $po->rcptd_qty_per_package,
                    $po->rcptd_qty_arr,
                    $po->rcptd_qty_appr,
                    $po->rcptd_qty_rej,
                    $po->rcptd_loc,
                    $po->rcptd_lot,
                    $po->rcptd_batch,
                ];
            }else{
                $this->flagpo = $po->po_nbr;
                $this->rcptnbr = $po->rcpt_nbr;
                return [
                    $po->po_domain,
                    $po->po_nbr,
                    $po->po_vend.' - '.$po->po_vend_desc,
                    $po->po_ship,
                    $po->po_ord_date,
                    $po->po_due_date,
                    $po->created_at,
                    // Receipt Master
                    $po->rcpt_nbr,
                    $po->rcpt_status,
                    $po->name,
                    // Receipt Checklist
                    $po->rcptc_imr_nbr,
                    $po->rcptc_article_nbr,
                    $po->rcptc_imr_date,
                    $po->rcptc_arrival_date,
                    $po->rcptc_prod_date,
                    $po->rcptc_exp_date,
                    $po->rcptc_manufacturer,
                    $po->rcptc_country,
                    // Receipt Document
                    $po->rcptdoc_is_certofanalys == 1 ? 'Yes' : 'No',
                    $po->rcptdoc_certofanalys,
                    $po->rcptdoc_is_msds == 1 ? 'Yes' : 'No',
                    $po->rcptdoc_msds,
                    $po->rcptdoc_is_forwarderdo == 1 ? 'Yes' : 'No',
                    $po->rcptdoc_forwarderdo,
                    $po->rcptdoc_is_packinglist == 1 ? 'Yes' : 'No',
                    $po->rcptdoc_packinglist,
                    $po->rcptdoc_is_otherdocs == 1 ? 'Yes' : 'No',
                    $po->rcptdoc_otherdocs,
                    // Receipt Kemasan
                    $po->rcptk_kemasan_sacdos == 1 ? 'Damage' : 'Not Damage',
                    $po->rcptk_kemasan_sacdos_desc,
                    $po->rcptk_kemasan_drumvat == 1 ? 'Damage' : 'Not Damage',
                    $po->rcptk_kemasan_drumvat_desc,
                    $po->rcptk_kemasan_palletpeti == 1 ? 'Damage' : 'Not Damage',
                    $po->rcptk_kemasan_palletpeti_desc,
                    $po->rcptk_is_clean == 1 ? 'Yes' : 'No',
                    $po->rcptk_is_clean_desc,
                    $po->rcptk_is_dry == 1 ? 'Yes' : 'No',
                    $po->rcptk_is_dry_desc,
                    $po->rcptk_is_not_spilled == 1 ? 'Yes' : 'No',
                    $po->rcptk_is_not_spilled_desc,
                    $po->rcptk_is_sealed == 1 ? 'Yes' : 'No',
                    $po->rcptk_is_manufacturer_label,
                    // Receipt Transport
                    $po->rcptt_transporter_no,
                    $po->rcptt_police_no,
                    $po->rcptt_is_clean == 1 ? 'Yes' : 'No',
                    $po->rcptt_is_clean_desc,
                    $po->rcptt_is_dry == 1 ? 'Yes' : 'No',
                    $po->rcptt_is_dry_desc,
                    $po->rcptt_is_not_spilled == 1 ? 'Yes' : 'No',
                    $po->rcptt_is_not_spilled_desc,
                    $po->rcptt_is_position_single == 1 ? 'Single' : 'Kombinasi',
                    $po->rcptt_is_position_single_desc,
                    $po->rcptt_is_segregated == 1 ? 'Yes' : 'No',
                    $po->rcptt_is_segregated_desc,
                    // Receipt Catatan
                    $po->rcptt_kelembapan,
                    $po->rcptt_suhu,
                    $po->rcptt_angkutan_catatan,
                    // Receipt Detail
                    $po->rcptd_line,
                    $po->rcptd_part.' - '.$po->rcptd_part_desc,
                    $po->rcptd_part_um,
                    $po->rcptd_qty_per_package,
                    $po->rcptd_qty_arr,
                    $po->rcptd_qty_appr,
                    $po->rcptd_qty_rej,
                    $po->rcptd_loc,
                    $po->rcptd_lot,
                    $po->rcptd_batch,
                ];
            }
        }

    }
    
    public function startCell(): string
    {
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
   
                $event->sheet->mergeCells('A1:A2');
                $event->sheet->setCellValue('A1', "PO Domain");
                $event->sheet->mergeCells('B1:B2');
                $event->sheet->setCellValue('B1', "PO Number");
                $event->sheet->mergeCells('C1:C2');
                $event->sheet->setCellValue('C1', "PO Vendor");
                $event->sheet->mergeCells('D1:D2');
                $event->sheet->setCellValue('D1', "PO Ship To");
                $event->sheet->mergeCells('E1:E2');
                $event->sheet->setCellValue('E1', "PO Order Date");
                $event->sheet->mergeCells('F1:F2');
                $event->sheet->setCellValue('F1', "PO Due Date");
                $event->sheet->mergeCells('G1:G2');
                $event->sheet->setCellValue('G1', "Created At");
                $event->sheet->mergeCells('H1:J1');
                $event->sheet->setCellValue('H1', "Receipt Master");
                $event->sheet->mergeCells('K1:R1');
                $event->sheet->setCellValue('K1', "Receipt Checklist");
                $event->sheet->mergeCells('S1:AB1');
                $event->sheet->setCellValue('S1', "Receipt Document");
                $event->sheet->mergeCells('AC1:AP1');
                $event->sheet->setCellValue('AC1', "Receipt Kemasan");
                $event->sheet->mergeCells('AQ1:BB1');
                $event->sheet->setCellValue('AQ1', "Receipt Transport");
                $event->sheet->mergeCells('BC1:BE1');
                $event->sheet->setCellValue('BC1', "Catatan");
                $event->sheet->mergeCells('BF1:BP1');
                $event->sheet->setCellValue('BF1', "Receipt Detail");

                $event->sheet->getDelegate()->getStyle('A1:BP2')
                            ->getAlignment()
                            ->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:BP2')
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->getStyle('A1:BP2')
                                ->getFont()
                                ->setBold(true);
   
            },
        ];
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
            'Receipt Number',
            'Receipt Status',
            'Receipt Created By',
            'IMR No',
            'Article No',
            'IMR Date',
            'Arrival Date',
            'Production Date',
            'Expiration Date',
            'Manufacturer',
            'Country',
            'Certificate Of Analysis',
            'Keterangan Certificate of Analysis',
            'MSDS',
            'Keterangan MSDS',
            'Forwarder',
            'Keterangan Forwarder',
            'Packing List',
            'Keterangan Packing List',
            'Other Docs',
            'Keterangan Other Docs',
            'Kemasan Sac / Dos',
            'Keterangan Kemasan Sac / Dos',
            'Kemasan Drum / Vat',
            'Keterangan Drum / Vat',
            'Kemasan Pallete / Peti',
            'Keterangan Pallete / Peti',
            'Kemasan Is Clean',
            'Keterangan',
            'Kemasan Is Dry',
            'Keterangan',
            'Kemasan Is Spilled',
            'Keterangan',
            'Kemasan Sealed',
            'Kemasan Manufacturer Label',
            'Transport No',
            'Police No',
            'Transport Is Clean',
            'Keterangan',
            'Transport Is Dry',
            'Keterangan',
            'Transport Is Spilled',
            'Keterangan',
            'Transport Position',
            'Keterangan',
            'Transport Segregated',
            'Keterangan',
            'Kelembapan',
            'Suhu',
            'Catatan',
            'Receipt Line',
            'Receipt Part',
            'Receipt UM',
            'Receipt Qty Per Package',
            'Receipt Qty Arrival',
            'Receipt Qty Approved',
            'Receipt Qty Reject',
            'Receipt Location',
            'Receipt Lot Serial',
            'Receipt Batch',
            'Receipt UM'
        ];
    }
}
