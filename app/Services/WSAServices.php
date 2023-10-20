<?php

namespace App\Services;

use App\Models\Master\Item;
use App\Models\Master\Location;
use App\Models\Master\Qxwsa;
use App\Models\Transaksi\PurchaseOrderDetail;
use App\Models\Transaksi\PurchaseOrderMaster;
use App\Models\Transaksi\ReceiptDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WSAServices
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

    public function changeStringtoInt(String $string){
        switch ($string){
            case 'Jan':
                return 1;
            case 'Feb':
                return 2;
            case 'Mar':
                return 3;
            case 'Apr':
                return 4;
            case 'May':
                return 5;
            case 'Jun':
                return 6;
            case 'Jul':
                return 7;
            case 'Aug':
                return 8;
            case 'Sep':
                return 9;
            case 'Oct':
                return 10;
            case 'Nov':
                return 11;
            case 'Dec':
                return 12;
        }
    }

    public function wsapo($ponbr)
    {
        $wsa = Qxwsa::first();

        $qxUrl = $wsa->wsas_url;
        $qxReceiver = '';
        $qxSuppRes = 'false';
        $qxScopeTrx = '';
        $qdocName = '';
        $qdocVersion = '';
        $dsName = '';
        $timeout = 10;
        $domain = $wsa->wsas_domain;
        $arrayloop = [];
        $qdocRequest =
            '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <phapros_get_po xmlns="' . $wsa->wsas_path . '">
                    <inpdomain>' . $domain . '</inpdomain>
                    <inpponbr>' . $ponbr . '</inpponbr>
                </phapros_get_po>
            </Body>
        </Envelope>';

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
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
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;

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
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');

        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

        if ($qdocResult == 'true') {
            $idmstr = '';
            $pomstr = PurchaseOrderMaster::query()
                ->where('po_domain', (string)$dataloop[0]->t_lvc_domain)
                ->where('po_nbr', (string)$dataloop[0]->t_lvc_nbr)
                ->first();
            $idmstr = $pomstr->id ?? '';
            if (!$pomstr) {

                $datamstr = [
                    'po_domain' => (string)$dataloop[0]->t_lvc_domain,
                    'po_nbr' => (string)$dataloop[0]->t_lvc_nbr,
                    'po_ship' => (string)$dataloop[0]->t_lvc_ship,
                    'po_site' => (string)$dataloop[0]->t_lvc_site,
                    'po_vend' => (string)$dataloop[0]->t_lvc_vend,
                    'po_ord_date' => (string)$dataloop[0]->t_lvt_ord,
                    'po_due_date' => (string)$dataloop[0]->t_lvt_due,
                    'po_curr' => (string)$dataloop[0]->t_lvc_curr,
                    'po_status' => (string)$dataloop[0]->t_lvc_status,
                    'po_vend_desc' => (string)$dataloop[0]->t_lvc_vend_desc,
                ];
                $idmstr = PurchaseOrderMaster::insertGetId($datamstr);
            }else{
                $pomstr->po_vend_desc = (string)$dataloop[0]->t_lvc_vend_desc;
                $pomstr->save();
            }

            foreach ($dataloop as $datas) {
                $poddet = PurchaseOrderDetail::query()
                    ->with('getMaster')
                    ->where('pod_po_id', $idmstr)
                    ->where('pod_line', (string)$datas->t_lvi_line)
                    ->where('pod_domain', (string)$datas->t_lvc_domain)
                    ->first();
                if ($poddet) {
                    // Update
                    $poddet->pod_qty_ord = (string)$datas->t_lvd_qtyord;
                    $poddet->pod_qty_rcvd = (string)$datas->t_lvd_qty_rcvd;
                    $poddet->pod_pur_cost = (string)$datas->t_lvd_price;
                    $poddet->pod_loc = (string)$datas->t_lvc_loc;
                    $poddet->pod_lot = (string)$datas->t_lvc_lot_next;
                    $poddet->save();
                } else {
                    // Insert
                    $datadetail = [
                        "pod_po_id" => $idmstr,
                        "pod_domain" => (string)$datas->t_lvc_domain,
                        "pod_line" => (string)$datas->t_lvi_line,
                        "pod_part" => (string)$datas->t_lvc_part,
                        "pod_desc" => (string)$datas->t_lvc_part_desc,
                        "pod_qty_ord" => (string)$datas->t_lvd_qtyord,
                        "pod_qty_rcvd" => (string)$datas->t_lvd_qty_rcvd,
                        "pod_pur_cost" => (string)$datas->t_lvd_price,
                        "pod_loc" => (string)$datas->t_lvc_loc,
                        "pod_lot" => (string)$datas->t_lvc_lot_next,
                    ];
                    $data = PurchaseOrderDetail::insert($datadetail);
                }

                $qtyreceive = 0;
                $qtyarrival = 0;
                
                $rcptdetail = ReceiptDetail::query()
                                ->with('getMaster')
                                ->where('rcptd_part', (string)$datas->t_lvc_part)
                                ->where('rcptd_line', (string)$datas->t_lvi_line)
                                ->whereRelation('getMaster','rcpt_po_id',$idmstr)
                                ->whereRelation('getMaster','rcpt_status','created')
                                ->get();
                
                foreach($rcptdetail as $rows){
                    $qtyreceive += $rows->rcptd_qty_appr;
                    $qtyarrival += $rows->rcptd_qty_arr;
                }

                $umkonv = (float)$datas->t_lvd_um_konv == 0 ? 1 : $datas->t_lvd_um_konv;

                $arrayloop[] = [
                    "t_lvc_nbr" => (string)$datas->t_lvc_nbr,
                    "t_lvc_domain" => (string)$datas->t_lvc_domain,
                    "t_lvc_ship" => (string)$datas->t_lvc_ship,
                    "t_lvc_site" => (string)$datas->t_lvc_site,
                    "t_lvc_vend" => (string)$datas->t_lvc_vend,
                    "t_lvc_vend_desc" => (string)$datas->t_lvc_vend_desc,
                    "t_lvt_ord" => (string)$datas->t_lvt_ord,
                    "t_lvt_due" => (string)$datas->t_lvt_due,
                    "t_lvc_curr" => (string)$datas->t_lvc_curr,
                    "t_lvd_totalline" => (string)$datas->t_lvd_totalline,
                    "t_lvc_status" => (string)$datas->t_lvc_status,
                    "t_lvi_line" => (string)$datas->t_lvi_line,
                    "t_lvc_part" => (string)$datas->t_lvc_part,
                    "t_lvc_part_desc" => (string)$datas->t_lvc_part_desc,
                    "t_lvd_qtyord" => (string)$datas->t_lvd_qtyord,
                    "t_lvd_qty_rcvd" => (string)$datas->t_lvd_qty_rcvd,
                    "t_lvd_price" => (string)$datas->t_lvd_price,
                    "t_lvc_loc" => (string)$datas->t_lvc_loc,
                    "t_lvc_lot_next" => (string)$datas->t_lvc_lot_next,
                    't_isSelected' => false,
                    "t_lvc_um" => (string)$datas->t_lvc_um,
                    "t_lvc_manufacturer" => (string)$datas->t_lvc_manufacturer,
                    "t_lvc_country" => (string)$datas->t_lvc_country,
                    "t_lvd_ongoing_qtyrcvd" => (string)$qtyreceive,
                    "t_lvd_ongoing_qtyarr" => (string)$qtyarrival,
                    "t_lvc_pt_um" => (string)$datas->t_lvc_pt_um,
                    "t_lvd_um_konv" => (string)$umkonv,
                ];
            }

            $arrayloop = collect($arrayloop);

            return $arrayloop;
        }
    }

    public function wsaloc()
    {
        $wsa = Qxwsa::first();

        $qxUrl = $wsa->wsas_url;
        $qxReceiver = '';
        $qxSuppRes = 'false';
        $qxScopeTrx = '';
        $qdocName = '';
        $qdocVersion = '';
        $dsName = '';
        $timeout = 10;
        $domain = $wsa->wsas_domain;
        $arrayloop = [];
        $qdocRequest =
            '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <phapros_loc_mstr xmlns="' . $wsa->wsas_path . '">
                    <inpdomain>' . $domain . '</inpdomain>
                </phapros_loc_mstr>
            </Body>
        </Envelope>';

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
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
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;

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
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');

        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];
        if ($qdocResult == 'true') {
            foreach ($dataloop as $key => $datas) {
                $arrayloop[] = [
                    "t_domain" => (string)$datas->t_domain,
                    "t_site" => (string)$datas->t_site,
                    "t_loc" => (string)$datas->t_loc,
                    "t_loc_desc" => (string)$datas->t_loc_desc,
                ];
            }

            $arrayloop = collect($arrayloop);

            return $arrayloop;
        }else{
            return false;
        }
    }

    public function wsaitem()
    {
        $wsa = Qxwsa::first();

        $qxUrl = $wsa->wsas_url;
        $qxReceiver = '';
        $qxSuppRes = 'false';
        $qxScopeTrx = '';
        $qdocName = '';
        $qdocVersion = '';
        $dsName = '';
        $timeout = 10;
        $domain = $wsa->wsas_domain;
        $arrayloop = [];
        $qdocRequest =
            '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <phapros_itm_mstr xmlns="' . $wsa->wsas_path . '">
                    <inpdomain>' . $domain . '</inpdomain>
                </phapros_itm_mstr>
            </Body>
        </Envelope>';

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
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
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;

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
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');

        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];
        if ($qdocResult == 'true') {
            foreach ($dataloop as $key => $datas) {
                // $arrayloop[] = [
                //     "t_domain" => (string)$datas->t_domain,
                //     "t_item_code" => (string)$datas->t_item_code,
                //     "t_item_desc" => (string)$datas->t_item_desc,
                // ];
                $item = Item::where('item_code',(string)$datas->t_item_code)->first();
                if(!$item){
                    $newitem = new Item();
                    $newitem->item_code = (string)$datas->t_item_code;
                    $newitem->item_desc = (string)$datas->t_item_desc;
                    $newitem->save();
                }
            }
            
            // $dataloop = collect($dataloop);
            // $arrayloop = collect($arrayloop);

            return $arrayloop;
        }else{
            return false;
        }
    }

    public function loadLocation()
    {
        $wsa = Qxwsa::first();

        $qxUrl = $wsa->wsas_url;
        $qxReceiver = '';
        $qxSuppRes = 'false';
        $qxScopeTrx = '';
        $qdocName = '';
        $qdocVersion = '';
        $dsName = '';
        $timeout = 10;
        $domain = $wsa->wsas_domain;
        $arrayloop = [];
        $qdocRequest =
            '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <phapros_loc_mstr xmlns="' . $wsa->wsas_path . '">
                    <inpdomain>' . $domain . '</inpdomain>
                </phapros_loc_mstr>
            </Body>
        </Envelope>';


        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
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
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;

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
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');

        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

        if ($qdocResult == 'true') {

            foreach ($dataloop as $datas) {
                $poddet = Location::firstOrNew(['domain' => $domain, 'loc' => (String)$datas->loc]);
                $poddet->loc_desc = (String)$datas->t_loc_desc;
                $poddet->site = (String)$datas->t_site;
                $poddet->save();
            }
            return true;
        } else {
            return false;
        }
    }

    public function wsamrp()
    {
        $wsa = Qxwsa::first();

        $qxUrl = $wsa->wsas_url;
        $qxReceiver = '';
        $qxSuppRes = 'false';
        $qxScopeTrx = '';
        $qdocName = '';
        $qdocVersion = '';
        $dsName = '';
        $timeout = 10;
        $domain = $wsa->wsas_domain;
        $arrayloop = [];
        $qdocRequest =
            '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <php_mrp_report xmlns="' . $wsa->wsas_path . '">
                    <inpdomain>' . $domain . '</inpdomain>
                </php_mrp_report>
            </Body>
        </Envelope>';

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
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
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;

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
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');

        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];
        if ($qdocResult == 'true') {
            foreach ($dataloop as $key => $datas) {
                $arrayloop[] = [
                    't_pt_prod_line' => (String)$datas->t_pt_prod_line,
                    't_mrp_type' => (String)$datas->t_mrp_type,
                    't_mrp_dataset' => (String)$datas->t_mrp_dataset,
                    't_mrp_part' => (String)$datas->t_mrp_part,
                    't_partdesc' => (String)$datas->t_partdesc,
                    't_mrp_nbr' => (String)$datas->t_mrp_nbr,
                    't_mrp_qty' => (String)$datas->t_mrp_qty,
                    't_ld_qty_oh' => (String)$datas->t_ld_qty_oh,
                    't_pt_pm_code' => (String)$datas->t_pt_pm_code,
                    't_mrp_rel_date' => (String)$datas->t_mrp_rel_date,
                    't_mrp_due_date' => (String)$datas->t_mrp_due_date,
                    't_pt_ord_per' => (String)$datas->t_pt_ord_per,
                    't_pt_ord_mult' => (String)$datas->t_pt_ord_mult,
                    't_pt_ord_min' => (String)$datas->t_pt_ord_min,
                    't_oa_det' => (String)$datas->t_oa_det,
                ];
            }

            $arrayloop = Collection::make($arrayloop);

            return $arrayloop;
        }else{
            return false;
        }
    }

    public function wsaRawMaterial()
    {
        $wsa = Qxwsa::first();

        $qxUrl = $wsa->wsas_url;
        $qxReceiver = '';
        $qxSuppRes = 'false';
        $qxScopeTrx = '';
        $qdocName = '';
        $qdocVersion = '';
        $dsName = '';
        $timeout = 10;
        $domain = $wsa->wsas_domain;
        $arrayloop = [];
        $qdocRequest =
            '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <php_ketersediaanRawMaterial xmlns="' . $wsa->wsas_path . '">
                    <inpdomain>' . $domain . '</inpdomain>
                </php_ketersediaanRawMaterial>
            </Body>
        </Envelope>';

        $curlOptions = array(
            CURLOPT_URL => $qxUrl,
            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
            CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
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
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;

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
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);

        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');

        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];
        

        // $dataloopcollection = collect($dataloop);
        // dd(gettype($dataloopcollection));
        // dd($dataloopcollection->whereIn('t_mrp_duedate',['Jun'])->sum('t_ld_qty_oh'));
        if ($qdocResult == 'true') {
            
            foreach ($dataloop as $key => $datas) {
                $arrayloop[] = [
                    "t_mrp_part"=> (String)$datas->t_mrp_part,
                    "t_mrp_duedate"=> (int)$this->changeStringtoInt($datas->t_mrp_duedate),
                    't_year_duedate' => (int)$datas->t_year_duedate,
                    "t_pt_desc1"=> (String)$datas->t_pt_desc1,
                    "t_pt_desc2"=> (String)$datas->t_pt_desc2,
                    "t_pt_um"=> (String)$datas->t_pt_um,
                    "t_ld_qty_oh"=> (float)$datas->t_ld_qty_oh,
                    "t_pod_qty_oh"=> (float)$datas->t_pod_qty_oh,
                    "t_rqm_qty"=> (float)$datas->t_rqm_qty,
                    "t_qty_bahan"=> (float)$datas->t_qty_bahan,
                    "t_qty_stok"=> (float)$datas->t_qty_stok,
                    
                ];
            }

            

            return $arrayloop;
        }else{
            return false;
        }
    }

    // public function wsainoac()
    // {
        
    //     $qxUrl = 'http://192.168.8.23:9399/wsasim/services/';
    //     $qxReceiver = '';
    //     $qxSuppRes = 'false';
    //     $qxScopeTrx = '';
    //     $qdocName = '';
    //     $qdocVersion = '';
    //     $dsName = '';
    //     $timeout = 10;
    //     $domain = '1000';
    //     $arrayloop = [];
    //     $qdocRequest =
    //         '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
    //         <Body>
    //             <getPoDetail xmlns="http://ws.iris.co.id/wsasim">
    //                 <iPoNbr>L2303059</iPoNbr>
    //             </getPoDetail>
    //         </Body>
    //     </Envelope>';

    //     $curlOptions = array(
    //         CURLOPT_URL => $qxUrl,
    //         CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
    //         CURLOPT_TIMEOUT => $timeout + 5, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
    //         CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
    //         CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
    //         CURLOPT_POST => true,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_SSL_VERIFYHOST => false
    //     );

    //     $getInfo = '';
    //     $httpCode = 0;
    //     $curlErrno = 0;
    //     $curlError = '';
    //     $qdocResponse = '';

    //     $curl = curl_init();
    //     if ($curl) {
    //         curl_setopt_array($curl, $curlOptions);
    //         $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
    //         $curlErrno    = curl_errno($curl);
    //         $curlError    = curl_error($curl);
    //         $first        = true;

    //         foreach (curl_getinfo($curl) as $key => $value) {
    //             if (gettype($value) != 'array') {
    //                 if (!$first) $getInfo .= ", ";
    //                 $getInfo = $getInfo . $key . '=>' . $value;
    //                 $first = false;
    //                 if ($key == 'http_code') $httpCode = $value;
    //             }
    //         }
    //         curl_close($curl);
    //     }

    //     if (is_bool($qdocResponse)) {
    //         return false;
    //     }
    //     $xmlResp = simplexml_load_string($qdocResponse);

    //     $xmlResp->registerXPathNamespace('ns1', 'http://ws.iris.co.id/wsasim');
    //     // $dataloop    = $xmlResp->xpath('//ns1:tempRow');

    //     $qdocResult =  $xmlResp->xpath('//ns1:oPoDetails')[0]; 
    //     $json = json_decode($qdocResult,true); 
    //     $hasil = $json['ttPoDetails'];
    //     $collecthasil = Collection::make($hasil);
    //     // return $collecthasil;
    //     return $hasil;
        

    // }


}
