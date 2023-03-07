<?php

namespace App\Services;

use App\Models\Master\Qxwsa;

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
                <phapros_get_po xmlns="'.$wsa->wsas_path.'">
                    <inpdomain>'.$domain.'</inpdomain>
                    <inpponbr>'.$ponbr.'</inpponbr>
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
        
        if(is_bool($qdocResponse)){
            return false;
        }
        $xmlResp = simplexml_load_string($qdocResponse);
        
        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');
        
        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];
        
        if($qdocResult == 'true'){
            foreach($dataloop as $datas){
                $arrayloop[] =                 
                [    
                    "t_lvc_nbr" => (String)$datas->t_lvc_nbr,
                    "t_lvc_domain" => (String)$datas->t_lvc_domain,
                    "t_lvc_ship" => (String)$datas->t_lvc_ship,
                    "t_lvc_site" => (String)$datas->t_lvc_site,
                    "t_lvc_vend" => (String)$datas->t_lvc_vend,
                    "t_lvc_vend_desc" => (String)$datas->t_lvc_vend_desc,
                    "t_lvt_ord" => (String)$datas->t_lvt_ord,
                    "t_lvt_due" => (String)$datas->t_lvt_due,
                    "t_lvc_curr" => (String)$datas->t_lvc_curr,
                    "t_lvd_totalline" => (String)$datas->t_lvd_totalline,
                    "t_lvc_status" => (String)$datas->t_lvc_status,
                    "t_lvi_line" => (String)$datas->t_lvi_line,
                    "t_lvc_part" => (String)$datas->t_lvc_part,
                    "t_lvc_part_desc" => (String)$datas->t_lvc_part_desc,
                    "t_lvd_qtyord" => (String)$datas->t_lvd_qtyord,
                    "t_lvd_qty_rcvd" => (String)$datas->t_lvd_qty_rcvd,
                    "t_lvd_price" => (String)$datas->t_lvd_price,
                    "t_lvc_loc" => (String)$datas->t_lvc_loc,
                    "t_lvc_lot_next" => (String)$datas->t_lvc_lot_next,
                    't_isSelected' => false,
                    "t_lvc_um" => (String)$datas->t_lvc_um
                ];
            }
            
            $arrayloop = collect($arrayloop);
        
            return $arrayloop;
        }
    }    
}
