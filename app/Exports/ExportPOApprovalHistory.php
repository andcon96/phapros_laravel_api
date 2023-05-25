<?php

namespace App\Exports;

use App\Models\Transaksi\PurchaseOrderMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportPOApprovalHistory implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private $flagpo = '';

    public function __construct($domain, $ponbr, $vendor, $shipto, $start_orddate, $end_orddate)
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
            ->join('approval_hist', 'po_mstr.po_nbr', '=', 'approval_hist.apphist_po_nbr')
            ->join('web_login_phapros.mst_anggota as db2', 'db2.id_anggota', '=', 'approval_hist.apphist_user_id')
            ->leftjoin('rcpt_mstr', 'rcpt_mstr.id', '=', 'approval_hist.apphist_receipt_id')
            ->select("*",'approval_hist.created_at as tanggal_approve');
        // ->with(['getDetail', 'getHistoryReceipt']);

        if ($domain) {
            $po->where('po_domain', $domain);
        }
        if ($ponbr) {
            $po->where('po_nbr', $ponbr);
        }
        if ($vendor) {
            $po->where('po_vend', $vendor);
        }
        if ($shipto) {
            $po->where('po_ship', $shipto);
        }
        if ($start_orddate) {
            $po->where('po_ord_date', '>=', $start_orddate);
        }
        if ($end_orddate) {
            $po->where('po_ord_date', '<=', $end_orddate);
        }

        $po = $po->get();
        
        
        return $po;
    }

    public function map($po): array
    {
        if ($this->flagpo == '') {
            $this->flagpo = $po->po_nbr;
            return [
                $po->po_domain,
                $po->po_nbr,
                $po->po_vend . ' - ' . $po->po_vend_desc,
                $po->rcpt_nbr,
                $po->nik . ' - ' . $po->nama,
                $po->apphist_status == null ? 'Waiting For Approval' : $po->apphist_status,
                // $po->apphist_approved_date,
                $po->tanggal_approve
            ];
        } else {
            if ($this->flagpo == $po->po_nbr) {
                $this->flagpo = $po->po_nbr;
                return [
                    '',
                    '',
                    '',
                    '',
                    $po->nik . ' - ' . $po->nama,
                    $po->apphist_status == null ? 'Waiting For Approval' : $po->apphist_status,
                    // $po->apphist_approved_date,
                    $po->tanggal_approve
                ];
            } else {
                $this->flagpo = $po->po_nbr;
                return [
                    $po->po_domain,
                    $po->po_nbr,
                    $po->po_vend . ' - ' . $po->po_vend_desc,
                    $po->rcpt_nbr,
                    $po->nik . ' - ' . $po->nama,
                    $po->apphist_status == null ? 'Waiting For Approval' : $po->apphist_status,
                    // $po->apphist_approved_date,
                    $po->tanggal_approve
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
            'Receipt Number',
            'User Approval',
            'Approval Status',
            'Approval Date'
        ];
    }
}
