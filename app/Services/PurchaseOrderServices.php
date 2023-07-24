<?php

namespace App\Services;

use App\Jobs\SendEmailPOApproval;
use App\Models\Master\Approval;
use App\Models\Master\Item;
use App\Models\Master\Prefix;
use App\Models\Master\PrefixIMR;
use App\Models\Master\Qxwsa;
use App\Models\Transaksi\ApprovalHist;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptChecklist;
use App\Models\Transaksi\ReceiptDetail;
use App\Models\Transaksi\ReceiptDocument;
use App\Models\Transaksi\ReceiptFileUpload;
use App\Models\Transaksi\ReceiptKemasan;
use App\Models\Transaksi\ReceiptMaster;
use App\Models\Transaksi\ReceiptTransport;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderServices
{
    private function httpHeader($req)
    {
        return array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req))
        );
    }

    public function savedetail($data)
    {
        $newdata = json_decode($data['data']);

        $ponbr = $newdata[0]->t_lvc_nbr ?? '';
        $domain = $newdata[0]->t_lvc_domain ?? '';

        DB::beginTransaction();
        try {
            // Get Prefix & PO
            $prefix = Prefix::firstOrFail();
            $rn = str_pad($prefix->prefix_rcpt_rn + 1, 6, 0, STR_PAD_LEFT);
            $newprefix = $prefix->prefix_rcpt_pr . $rn;

            $pomstr = PurchaseOrderMaster::query()
                ->where('po_nbr', $ponbr)
                ->where('po_domain', $domain)
                ->firstOrFail();

            // Save Receipt Master
            $poreceipt = new ReceiptMaster();
            $poreceipt->rcpt_po_id = $pomstr->id;
            $poreceipt->rcpt_domain = $domain;
            $poreceipt->rcpt_nbr = $newprefix;
            $poreceipt->rcpt_status = 'created';
            $poreceipt->rcpt_user_id  = $data['id_anggota'] ?? '';
            $poreceipt->rcpt_date  = Carbon::now()->toDateString();
            $poreceipt->save();

            $idrcpmstr = $poreceipt->id;

            // Save Receipt Detail
            foreach ($newdata as $key => $datas) {
                // $arraysiteloc = explode(' || ', $datas->t_lvc_loc);
                // $site = $arraysiteloc[0] ?? '';
                // $loc = $arraysiteloc[1] ?? '';

                $loc = $datas->t_lvc_loc;

                $detailreceipt = new ReceiptDetail();
                $detailreceipt->rcptd_rcpt_id = $idrcpmstr;
                $detailreceipt->rcptd_line = $datas->t_lvi_line;
                $detailreceipt->rcptd_part = $datas->t_lvc_part;
                $detailreceipt->rcptd_part_desc = $datas->t_lvc_part_desc;
                $detailreceipt->rcptd_part_um = $datas->t_lvc_um;
                $detailreceipt->rcptd_qty_arr = $datas->t_lvd_qty_datang;
                $detailreceipt->rcptd_qty_appr = $datas->t_lvd_qty_terima;
                $detailreceipt->rcptd_qty_rej = $datas->t_lvd_qty_reject;
                $detailreceipt->rcptd_qty_per_package = $datas->t_lvd_qty_per_package;
                // $detailreceipt->rcptd_loc = $datas->t_lvc_loc;
                $detailreceipt->rcptd_loc = $loc;
                // $detailreceipt->rcptd_site = $site;
                $detailreceipt->rcptd_exp_date = $datas->t_lvc_exp_detail_date;
                $detailreceipt->rcptd_manu_date = $datas->t_lvc_manu_detail_date;
                $detailreceipt->rcptd_lot = $datas->t_lvc_lot;
                $detailreceipt->rcptd_batch = $datas->t_lvc_batch;
                $detailreceipt->save();
            }

            // Save Checklist
            // Validasi IMO No apakah sudah ada di DB & Update Prefix RN IMR
            $imrno = $data['imrno'];
            $yearnow = Carbon::now()->format('y');

            $cekchecklist = ReceiptChecklist::where('rcptc_imr_nbr',$data['imrno'])->first();
            if($cekchecklist){
                $totalrn = 1;
                $newrn = 0;
                $rnItem = 0;
                // Prefix Item
                $itemcode = $data['itemcode'];
                $dataitem = Item::where('item_code',$itemcode)->first();
                
                if($dataitem){
                    $rnItem = intval(substr($dataitem->item_rn,2,4)) + 1;
                    $rnItem = str_pad($rnItem,2,0,STR_PAD_LEFT);
                }

                // Prefix IMR
                $allprefix = PrefixIMR::get();
                foreach($allprefix as $allprefixs){
                    // Cek Tahun Sama / ga
                    if($yearnow == substr($allprefixs->pin_rn,0,2)){
                        // Hitung Total Tahun Berjalan
                        $totalrn += intval(substr($allprefixs->pin_rn,2,4));
                        
                        // Hitung Total Tahun Berjalan sesuai prefix inputan.
                        if($allprefixs->pin_prefix == substr($imrno,0,2)){    
                            $newrn = intval(substr($allprefixs->pin_rn,2,4)) + 1;
                            $newrn = str_pad($newrn,3,0,STR_PAD_LEFT);   // 2 Jadi 3
                        }
                    }
                }
                $totalrn = str_pad($totalrn,3,0,STR_PAD_LEFT);

                // $imrno = substr($imrno,0,2). '/' . $newrn . '/' . $totalrn;
                $imrno = substr($imrno,0,2). '/' . $rnItem . '/' . $newrn;
            }
            
            $checklist = new ReceiptChecklist();
            $checklist->rcptc_rcpt_id = $idrcpmstr;
            $checklist->rcptc_imr_nbr = $imrno;
            $checklist->rcptc_article_nbr = $data['articleno'];
            $checklist->rcptc_imr_date = $data['imrdate'];
            $checklist->rcptc_arrival_date = $data['arrivaldate'];
            $checklist->rcptc_prod_date = $data['productiondate'];
            $checklist->rcptc_exp_date = $data['expiredate'];
            $checklist->rcptc_manufacturer = $data['manufacturer'];
            $checklist->rcptc_country = $data['country'];
            $checklist->save();

            // Save Prefix IMR & Prefix Item
            $usedprefix = PrefixIMR::where('pin_prefix',substr($imrno,0,2))->first();
            if($usedprefix){
                if(substr($usedprefix->pin_rn,0,2) == $yearnow){
                    $newrn = $usedprefix->pin_rn + 1;
                    $usedprefix->pin_rn = $newrn;
                }else{
                    $usedprefix->pin_rn = $yearnow.'0001';
                }
                $usedprefix->save();
            }
            $usedprefixItem = Item::where('item_code',$data['itemcode'])->first();
            if($usedprefixItem){
                if(substr($usedprefixItem->item_rn,0,2) == $yearnow){
                    $newrn = $usedprefixItem->item_rn + 1;
                    $usedprefixItem->item_rn = $newrn;
                }else{
                    $usedprefixItem->item_rn = $yearnow.'0001';
                }
                $usedprefixItem->save();
            }

            // Save Document
            $document = new ReceiptDocument();
            $document->rcptdoc_rcpt_id  = $idrcpmstr;
            $document->rcptdoc_is_certofanalys = $data['is_certofanalys'] == 'true' ? 1 : 0;
            $document->rcptdoc_certofanalys = $data['certofanalys'];
            $document->rcptdoc_is_msds = $data['is_msds'] == 'true' ? 1 : 0;
            $document->rcptdoc_msds = $data['msds'];
            $document->rcptdoc_is_forwarderdo = $data['is_forwarderdo'] == 'true' ? 1 : 0;
            $document->rcptdoc_forwarderdo = $data['forwarderdo'];
            $document->rcptdoc_is_packinglist = $data['is_packinglist'] == 'true' ? 1 : 0;
            $document->rcptdoc_packinglist = $data['packinglist'];
            $document->rcptdoc_is_otherdocs = $data['is_otherdocs'] == 'true' ? 1 : 0;
            $document->rcptdoc_otherdocs = $data['otherdocs'];
            $document->save();

            // Save Kemasan
            $kemasan = new ReceiptKemasan();
            $kemasan->rcptk_rcpt_id = $idrcpmstr;
            $kemasan->rcptk_kemasan_sacdos = $data['kemasan_sacdos'] == 'true' ? 1 : 0;
            $kemasan->rcptk_kemasan_sacdos_desc = $data['is_damage_kemasan_sacdos'];
            $kemasan->rcptk_kemasan_drumvat = $data['kemasan_drumvat'] == 'true' ? 1 : 0;
            $kemasan->rcptk_kemasan_drumvat_desc = $data['is_damage_kemasan_drumvat'];
            $kemasan->rcptk_kemasan_palletpeti = $data['kemasan_palletpeti'] == 'true' ? 1 : 0;
            $kemasan->rcptk_kemasan_palletpeti_desc = $data['is_damage_kemasan_palletpeti'];
            $kemasan->rcptk_is_clean = $data['is_clean'] == 'null' ? 1 : $data['is_clean'];
            $kemasan->rcptk_is_clean_desc = $data['keterangan_is_clean'];
            $kemasan->rcptk_is_dry = $data['is_dry'] == 'null' ? 1 : $data['is_dry'];
            $kemasan->rcptk_is_dry_desc = $data['keterangan_is_dry'];
            $kemasan->rcptk_is_not_spilled = $data['is_not_spilled'] == 'null' ? 1 : $data['is_not_spilled'];
            $kemasan->rcptk_is_not_spilled_desc = $data['keterangan_is_not_spilled'];
            $kemasan->rcptk_is_sealed = $data['is_sealed'] == 'null' ? 1 : 0;
            $kemasan->rcptk_is_manufacturer_label = $data['is_manufacturer_label'] == 'null' ? 1 : $data['is_manufacturer_label'];
            $kemasan->save();

            // Save Angkutan
            $angkutan = new ReceiptTransport();
            $angkutan->rcptt_rcpt_id  = $idrcpmstr;
            $angkutan->rcptt_transporter_no  = $data['transporter_no'];
            $angkutan->rcptt_police_no  = $data['police_no'];
            $angkutan->rcptt_is_clean  = $data['is_clean_angkutan'] == 'null' ? 1 : $data['is_clean_angkutan'];
            $angkutan->rcptt_is_clean_desc  = $data['keterangan_is_clean_angkutan'];
            $angkutan->rcptt_is_dry  = $data['is_dry_angkutan'] == 'null' ? 1 : $data['is_dry_angkutan'];
            $angkutan->rcptt_is_dry_desc  = $data['keterangan_is_dry_angkutan'];
            $angkutan->rcptt_is_not_spilled  = $data['is_not_spilled_angkutan'] == 'null' ? 1 : $data['is_not_spilled_angkutan'];
            $angkutan->rcptt_is_not_spilled_desc  = $data['keterangan_is_not_spilled_angkutan'];
            $angkutan->rcptt_is_position_single  = $data['material_position'] == 'null' ? 1 : $data['material_position'];
            $angkutan->rcptt_is_position_single_desc  = $data['keterangan_material_position'];
            $angkutan->rcptt_is_segregated  = $data['is_segregated'] == 'null' ? 1 : $data['is_segregated'];
            $angkutan->rcptt_is_segregated_desc  = $data['keterangan_is_segregated'];
            $angkutan->rcptt_angkutan_catatan = $data['angkutan_catatan'];
            $angkutan->rcptt_kelembapan = $data['kelembapan'];
            $angkutan->rcptt_suhu = $data['suhu'];
            $angkutan->save();

            // TTD Driver
            if ($data['signature'] != '') {
                $imagettd = base64_decode($data['signature']);

                if ($imagettd) {
                    $dataTime = date('Ymd_His');
                    $filename = $dataTime . '-TTD-' . $data['nik'] . '.png';

                    // Simpan File Upload pada Public
                    $savepath = public_path('/uploadttd/');

                    $fullfile = $savepath . $filename;
                    file_put_contents($fullfile, $imagettd);

                    $newdata = new ReceiptFileUpload();
                    $newdata->rcptfu_rcpt_id = $idrcpmstr;
                    $newdata->rcptfu_path = '/uploadttd/'.$filename;
                    $newdata->rcptfu_is_ttd = 1;
                    $newdata->save();
                }
            }

            // Upload File
            if (array_key_exists('images', $data)) {
                foreach ($data['images'] as $key => $dataImage) {
                    if ($dataImage->isValid()) {
                        $dataTime = date('Ymd_His');
                        $filename = $dataTime . '-' . $dataImage->getClientOriginalName();

                        // Simpan File Upload pada Public
                        $savepath = public_path('/uploadfile/');
                        $dataImage->move($savepath, $filename);

                        $fullfile = $savepath.$filename;
                        
                        $newdata = new ReceiptFileUpload();
                        $newdata->rcptfu_rcpt_id = $idrcpmstr;
                        $newdata->rcptfu_path = '/uploadfile/'.$filename;
                        $newdata->rcptfu_is_ttd = 0;
                        $newdata->save();
                    }
                }
            }

            // Create Approval
            // $approval = Approval::orderBy('approval_order', 'ASC')->get();

            // foreach ($approval as $key => $data) {
            //     $apphist = new ApprovalHist();
            //     $apphist->apphist_user_id = $data->approval_user_id;
            //     $apphist->apphist_po_domain = $domain;
            //     $apphist->apphist_receipt_id = $idrcpmstr;
            //     $apphist->apphist_po_nbr = $ponbr;
            //     $apphist->save();
            // }

            // Update Prefix
            $prefix->prefix_rcpt_rn = $rn;
            $prefix->save();

            DB::commit();
            return [true, $idrcpmstr];
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('savepo')->info($e);
            return [false];
        }
    }

    public function saveEditDetail($data)
    {
        $newdata = json_decode($data['data']);

        $idmaster = '';

        DB::beginTransaction();
        try {
            // Save Receipt Detail
            foreach ($newdata as $key => $datas) {
                $loc = $datas->t_lvc_loc;
                $detailreceipt = ReceiptDetail::find($datas->t_lvc_nbr);
                if($detailreceipt){
                    $idmaster = $detailreceipt->rcptd_rcpt_id;

                    $detailreceipt->rcptd_qty_arr = $datas->t_lvd_qty_datang;
                    $detailreceipt->rcptd_qty_appr = $datas->t_lvd_qty_rcvd;
                    $detailreceipt->rcptd_qty_rej = $datas->t_lvd_qty_reject;
                    $detailreceipt->rcptd_loc = $loc;
                    $detailreceipt->rcptd_exp_date = $datas->t_lvc_exp_detail_date;
                    $detailreceipt->rcptd_manu_date = $datas->t_lvc_manu_detail_date;
                    $detailreceipt->rcptd_lot = $datas->t_lvc_lot;
                    $detailreceipt->rcptd_batch = $datas->t_lvc_batch;
                    $detailreceipt->save();
                }
            }

            // Rubah Status Master
            $cekmaster = ReceiptMaster::find($idmaster);
            if($cekmaster){
                $cekmaster->rcpt_status = 'created';
                $cekmaster->save();
            }

            // Save Checklist
            // Validasi IMO No apakah sudah ada di DB & Update Prefix RN IMR
            $imrno = $data['imrno'];
            $yearnow = Carbon::now()->format('y');

            $cekchecklist = ReceiptChecklist::where('rcptc_rcpt_id',$idmaster)->first();
            if($cekchecklist){
                $cekchecklist->rcptc_article_nbr = $data['articleno'];
                $cekchecklist->rcptc_imr_date = $data['imrdate'];
                $cekchecklist->rcptc_arrival_date = $data['arrivaldate'];
                $cekchecklist->rcptc_prod_date = $data['productiondate'];
                $cekchecklist->rcptc_exp_date = $data['expiredate'];
                $cekchecklist->rcptc_manufacturer = $data['manufacturer'];
                $cekchecklist->rcptc_country = $data['country'];
                $cekchecklist->save();
            }
            
            $cekdocument = ReceiptDocument::where('rcptdoc_rcpt_id',$idmaster)->First();
            if($cekdocument){
                $cekdocument->rcptdoc_is_certofanalys = $data['is_certofanalys'] == 'true' ? 1 : 0;
                $cekdocument->rcptdoc_certofanalys = $data['certofanalys'];
                $cekdocument->rcptdoc_is_msds = $data['is_msds'] == 'true' ? 1 : 0;
                $cekdocument->rcptdoc_msds = $data['msds'];
                $cekdocument->rcptdoc_is_forwarderdo = $data['is_forwarderdo'] == 'true' ? 1 : 0;
                $cekdocument->rcptdoc_forwarderdo = $data['forwarderdo'];
                $cekdocument->rcptdoc_is_packinglist = $data['is_packinglist'] == 'true' ? 1 : 0;
                $cekdocument->rcptdoc_packinglist = $data['packinglist'];
                $cekdocument->rcptdoc_is_otherdocs = $data['is_otherdocs'] == 'true' ? 1 : 0;
                $cekdocument->rcptdoc_otherdocs = $data['otherdocs'];
                $cekdocument->save();
            }

            // Save Kemasan
            $cekkemasan = ReceiptKemasan::where('rcptk_rcpt_id',$idmaster)->first();
            if($cekkemasan){
                $cekkemasan->rcptk_kemasan_sacdos = $data['kemasan_sacdos'] == 'true' ? 1 : 0;
                $cekkemasan->rcptk_kemasan_sacdos_desc = $data['is_damage_kemasan_sacdos'];
                $cekkemasan->rcptk_kemasan_drumvat = $data['kemasan_drumvat'] == 'true' ? 1 : 0;
                $cekkemasan->rcptk_kemasan_drumvat_desc = $data['is_damage_kemasan_drumvat'];
                $cekkemasan->rcptk_kemasan_palletpeti = $data['kemasan_palletpeti'] == 'true' ? 1 : 0;
                $cekkemasan->rcptk_kemasan_palletpeti_desc = $data['is_damage_kemasan_palletpeti'];
                $cekkemasan->rcptk_is_clean = $data['is_clean'] == 'null' ? 1 : $data['is_clean'];
                $cekkemasan->rcptk_is_clean_desc = $data['keterangan_is_clean'];
                $cekkemasan->rcptk_is_dry = $data['is_dry'] == 'null' ? 1 : $data['is_dry'];
                $cekkemasan->rcptk_is_dry_desc = $data['keterangan_is_dry'];
                $cekkemasan->rcptk_is_not_spilled = $data['is_not_spilled'] == 'null' ? 1 : $data['is_not_spilled'];
                $cekkemasan->rcptk_is_not_spilled_desc = $data['keterangan_is_not_spilled'];
                $cekkemasan->rcptk_is_sealed = $data['is_sealed'] == 'null' ? 1 : 0;
                $cekkemasan->rcptk_is_manufacturer_label = $data['is_manufacturer_label'] == 'null' ? 1 : $data['is_manufacturer_label'];
                $cekkemasan->save();
            }

            // Save Angkutan
            $cektransport = ReceiptTransport::where('rcptt_rcpt_id',$idmaster)->first();
            if($cektransport){
                $cektransport->rcptt_transporter_no  = $data['transporter_no'];
                $cektransport->rcptt_police_no  = $data['police_no'];
                $cektransport->rcptt_is_clean  = $data['is_clean_angkutan'] == 'null' ? 1 : $data['is_clean_angkutan'];
                $cektransport->rcptt_is_clean_desc  = $data['keterangan_is_clean_angkutan'];
                $cektransport->rcptt_is_dry  = $data['is_dry_angkutan'] == 'null' ? 1 : $data['is_dry_angkutan'];
                $cektransport->rcptt_is_dry_desc  = $data['keterangan_is_dry_angkutan'];
                $cektransport->rcptt_is_not_spilled  = $data['is_not_spilled_angkutan'] == 'null' ? 1 : $data['is_not_spilled_angkutan'];
                $cektransport->rcptt_is_not_spilled_desc  = $data['keterangan_is_not_spilled_angkutan'];
                $cektransport->rcptt_is_position_single  = $data['material_position'] == 'null' ? 1 : $data['material_position'];
                $cektransport->rcptt_is_position_single_desc  = $data['keterangan_material_position'];
                $cektransport->rcptt_is_segregated  = $data['is_segregated'] == 'null' ? 1 : $data['is_segregated'];
                $cektransport->rcptt_is_segregated_desc  = $data['keterangan_is_segregated'];
                $cektransport->rcptt_angkutan_catatan = $data['angkutan_catatan'];
                $cektransport->rcptt_kelembapan = $data['kelembapan'];
                $cektransport->rcptt_suhu = $data['suhu'];
                $cektransport->save();
            }

            // Upload File
            if (array_key_exists('images', $data)) {
                foreach ($data['images'] as $key => $dataImage) {
                    if ($dataImage->isValid()) {
                        $dataTime = date('Ymd_His');
                        $filename = $dataTime . '-' . $dataImage->getClientOriginalName();

                        // Simpan File Upload pada Public
                        $savepath = public_path('/uploadfile/');
                        $dataImage->move($savepath, $filename);

                        $fullfile = $savepath.$filename;
                        
                        $newdata = new ReceiptFileUpload();
                        $newdata->rcptfu_rcpt_id = $idmaster;
                        $newdata->rcptfu_path = '/uploadfile/'.$filename;
                        $newdata->rcptfu_is_ttd = 0;
                        $newdata->save();
                    }
                }
            }

            DB::commit();
            return [true, $idmaster];
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('savepo')->info($e);
            return [false];
        }
    }

    public function sendmailapproval($receiptid)
    {
        $datareceipt = ReceiptMaster::with('getpo','getDetail','getUser')->findOrFail($receiptid);
        SendEmailPOApproval::dispatch(
            $datareceipt
        );

        return true;
    }
    
    public function qxPurchaseOrderReceipt($data)
    {
        $ponbr = $data['getpo']['po_nbr'];

        $domain = $data['rcpt_domain'];
        $qxwsa = Qxwsa::firstOrFail();
        
        if (is_null($qxwsa->qx_url)) {
            return 'nourl';
        }
        // Var Qxtend
        $qxUrl          = $qxwsa->qx_url;

        $timeout        = 0;

        // XML Qextend
        $qdocHead = '<?xml version="1.0" encoding="UTF-8"?>
                        <soapenv:Envelope xmlns="urn:schemas-qad-com:xml-services"
                        xmlns:qcom="urn:schemas-qad-com:xml-services:common"
                        xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
                        <soapenv:Header>
                            <wsa:Action/>
                            <wsa:To>urn:services-qad-com:QXPURCH</wsa:To>
                            <wsa:MessageID>urn:services-qad-com::QXPURCH</wsa:MessageID>
                            <wsa:ReferenceParameters>
                            <qcom:suppressResponseDetail>true</qcom:suppressResponseDetail>
                            </wsa:ReferenceParameters>
                            <wsa:ReplyTo>
                            <wsa:Address>urn:services-qad-com:</wsa:Address>
                            </wsa:ReplyTo>
                        </soapenv:Header>
                        <soapenv:Body>
                            <receivePurchaseOrder>
                            <qcom:dsSessionContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>domain</qcom:propertyName>
                                <qcom:propertyValue>' . $domain . '</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                <qcom:propertyValue>true</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>version</qcom:propertyName>
                                <qcom:propertyValue>eB_2</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
                                <qcom:propertyValue>false</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>action</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>entity</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>email</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>emailLevel</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                            </qcom:dsSessionContext>';

            $qdocbody = '<dsPurchaseOrderReceive>
                            <purchaseOrderReceive>
                            <ordernum>' . $ponbr . '</ordernum>
                            <psNbr>'.$data["getChecklist"]["rcptc_imr_nbr"].'</psNbr>
                            <yn>true</yn>
                            <yn1>true</yn1>';
    
            foreach ($data['getDetail'] as $key => $datas) {
                $qdocbody .=     '<lineDetail>
                                    <line>' . $datas['rcptd_line'] . '</line>
                                    <lotserialQty>' . $datas['rcptd_qty_appr'] . '</lotserialQty>
                                    <packingQty>' .$datas['rcptd_qty_per_package']. '</packingQty>
                                    <location>' . $datas['rcptd_loc'] . '</location>
                                    <lotserial>' . $datas['rcptd_lot'] . '</lotserial>
                                    <lotref>' .$datas['rcptd_qty_per_package']. '</lotref>
                                    <podQad04>' .$datas['rcptd_batch']. '</podQad04>
                                    <multiEntry>false</multiEntry>
                                    <chgAttr>true</chgAttr>
                                    <chgExpire>'.$datas['rcptd_exp_date'].'</chgExpire>
                                    <serialsYn>true</serialsYn>
                                    
                                    
                                </lineDetail>';
            }

        $qdocfoot = '</purchaseOrderReceive>
        </dsPurchaseOrderReceive>
        </receivePurchaseOrder>
                        </soapenv:Body>
                    </soapenv:Envelope>';

        $qdocRequest = $qdocHead . $qdocbody . $qdocfoot;

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
            CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
            CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        $getInfo = '';
        $httpCode = 0;
        $curlErrno = 0;
        $curlError = '';


        $qdocResponse = '';

        $curl = curl_init();
        if ($curl) {
            curl_setopt_array($curl, $curlOptions);
            $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
            //
            $curlErrno = curl_errno($curl);
            $curlError = curl_error($curl);
            $first = true;
            foreach (curl_getinfo($curl) as $key => $value) {
                if (gettype($value) != 'array') {
                    if (!$first) $getInfo .= ", ";
                    $getInfo = $getInfo . $key . '=>' . $value;
                    $first = false;
                    if ($key == 'http_code') $httpCode = $value;
                }
            }
            curl_close($curl);
        }

        if (is_bool($qdocResponse)) {
            Log::channel('qxtendReceipt')->info('rcpt_nbr: ' . $data['rcpt_nbr'] . ' qxtend connection error');
            return 'failed';
        }
        // dd($qdocResponse, $qdocRequest);

        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
        
        $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];

        if ($qdocResult == "success" or $qdocResult == "warning") {
            return 'success';
        } else {
            Log::channel('qxtendReceipt')->info('rcpt_nbr: ' . $data['rcpt_nbr'] . ' ' . $qdocRequest);
            Log::channel('qxtendReceipt')->info('rcpt_nbr: ' . $data['rcpt_nbr'] . ' ' . $qdocResponse);
            return 'failed';
        }
    }
/*
    public function loadpodaily($data)
    {
        $qxwsa = Qxwsa::firstOrFail();
        if (is_null($qxwsa->qx_url)) {
            return 'nourl';
        }

        $ponbr = $data['po_nbr'];
        $povend = $data['po_vend'];

        $poddetail = $data['poDetails'];

        // Var Qxtend
        $qxUrl          = $qxwsa->qx_url;

        $timeout        = 0;

        // XML Qextend
        $qdocHead = '<?xml version="1.0" encoding="UTF-8"?>
                        <soapenv:Envelope xmlns="urn:schemas-qad-com:xml-services"
                        xmlns:qcom="urn:schemas-qad-com:xml-services:common"
                        xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
                        <soapenv:Header>
                            <wsa:Action/>
                            <wsa:To>urn:services-qad-com:QADHSS</wsa:To>
                            <wsa:MessageID>urn:services-qad-com::QADHSS</wsa:MessageID>
                            <wsa:ReferenceParameters>
                            <qcom:suppressResponseDetail>false</qcom:suppressResponseDetail>
                            </wsa:ReferenceParameters>
                            <wsa:ReplyTo>
                            <wsa:Address>urn:services-qad-com:</wsa:Address>
                            </wsa:ReplyTo>
                        </soapenv:Header>
                        <soapenv:Body>
                            <maintainSalesOrder>
                            <qcom:dsSessionContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>domain</qcom:propertyName>
                                <qcom:propertyValue>'.Session::get('domain').'</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                <qcom:propertyValue>true</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>version</qcom:propertyName>
                                <qcom:propertyValue>ERP3_2</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
                                <qcom:propertyValue>false</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>username</qcom:propertyName>
                                <qcom:propertyValue>mfg</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>password</qcom:propertyName>
                                <qcom:propertyValue></qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>action</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>entity</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>email</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                <qcom:propertyName>emailLevel</qcom:propertyName>
                                <qcom:propertyValue/>
                                </qcom:ttContext>
                            </qcom:dsSessionContext>';

        $qdocbody = '<dsPurchaseOrder>
                        <PurchaseOrder>
                            <poNbr>' . $ponbr . '</poNbr>
                            <poVend>' . $povend . '</poVend>';

                    foreach ($poddetail as $key => $datas) {
                    $qdocbody .=    '<PurchaseOrderDetail>' .
                            '<line>' . $datas['pod_line'] . '</line>' .
                            '<sodPart>' . $datas['pod_part'] . '</sodPart>' .
                            '<sodQtyOrd>' . $datas['pod_qty_ord'] . '</sodQtyOrd>' .                   
                            '</PurchaseOrderDetail>';
                    }

        $qdocbody .=   '</PurchaseOrder>
                                            </dsPurchaseOrder>';

        $qdocfoot = '</maintainPurchaseOrder>
                        </soapenv:Body>
                    </soapenv:Envelope>';

        $qdocRequest = $qdocHead . $qdocbody . $qdocfoot;

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
            CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
            CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        $getInfo = '';
        $httpCode = 0;
        $curlErrno = 0;
        $curlError = '';


        $qdocResponse = '';

        $curl = curl_init();
        if ($curl) {
            curl_setopt_array($curl, $curlOptions);
            $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
            //
            $curlErrno = curl_errno($curl);
            $curlError = curl_error($curl);
            $first = true;
            foreach (curl_getinfo($curl) as $key => $value) {
                if (gettype($value) != 'array') {
                if (!$first) $getInfo .= ", ";
                $getInfo = $getInfo . $key . '=>' . $value;
                $first = false;
                if ($key == 'http_code') $httpCode = $value;
                }
            }
            curl_close($curl);
        }

        if (is_bool($qdocResponse)) {
            // Update Status kalau gagal.
            $poMaster = PurchaseOrderMaster::where('po_nbr',$ponbr)->first();
            $poMaster->po_status = 0;
            $poMaster->save();
            return false;
        }

        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
        $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];

        if ($qdocResult == "success" or $qdocResult == "warning") {
            return [$qdocResult, $qdocResponse];
        } else {
            
            $poMaster = PurchaseOrderMaster::where('po_nbr',$ponbr)->first();
            $poMaster->po_status = 0;
            $poMaster->save();
            return [$qdocResult, $qdocResponse];
        }
    }
    */
}
