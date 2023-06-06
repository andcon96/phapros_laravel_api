<?php

namespace App\Console\Commands;

use App\Models\Master\Qxwsa;
use App\Models\Transaksi\ForecastRencanaProduksi;
use App\Models\Transaksi\ItemMRP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoadRencanaProduksi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:rencanaProduksi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Rencana Produksi harian';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

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

    public function handle()
    {
        // return 0;

        try {
            $wsa = Qxwsa::first();
            $wsa_url          = $wsa->wsas_url;
            $qxReceiver     = '';
            $qxSuppRes      = 'false';
            $qxScopeTrx     = '';
            $qdocName       = '';
            $qdocVersion    = '';
            $dsName         = '';
            $timeout        = 0;

            $domain         = $wsa->wsas_domain;

            // Wsa ambil dari item dan stock
            $qdocRequest = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                <Body>
                                    <php_stockForecast xmlns="' . $wsa->wsas_path . '">
                                        <inpdomain>' . $domain . '</inpdomain>
                                    </php_stockForecast>
                                </Body>
                            </Envelope>';


            $curlOptions = array(
                CURLOPT_URL => $wsa_url,
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

            $xmlResp = simplexml_load_string($qdocResponse);

            $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);

            $dataloop   = $xmlResp->xpath('//ns1:tempRow');
            $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

            DB::beginTransaction();

            // dd($qdocResult);
            if ($qdocResult == 'true') {
                $rencanaProduksi = ForecastRencanaProduksi::count();
                if ($rencanaProduksi > 0) {
                    DB::table('forecast_rencana_produksi')->delete();
                }

                $itemExists = ItemMRP::count();
                if ($itemExists > 0) {
                    DB::table('item_m_r_p')->delete();
                }
                
                foreach ($dataloop as $dataWSA) {
                    $rencanaProduksi = new ItemMRP();
                    $rencanaProduksi->item_code = $dataWSA->t_pt_part;
                    $rencanaProduksi->item_design_group = $dataWSA->t_pt_dsgn_group;
                    $rencanaProduksi->item_description = $dataWSA->t_pt_desc;
                    $rencanaProduksi->item_pareto = $dataWSA->t_pt_pareto;
                    $rencanaProduksi->item_make_to_type = $dataWSA->t_pt_maketo;
                    $rencanaProduksi->item_ed = $dataWSA->t_pt_ed;
                    $rencanaProduksi->item_bv = $dataWSA->t_pt_bv;
                    $rencanaProduksi->item_last_stock_date = (string)$dataWSA->t_in_last_issue_date == '' ? NULL : (string)$dataWSA->t_in_last_issue_date;
                    $rencanaProduksi->item_stock_qty_oh = $dataWSA->t_in_qty_oh;
                    $rencanaProduksi->save();
                }

                // WSA untuk ambil forecast
                $qdocRequest = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                    <Body>
                                        <php_forecast xmlns="' . $wsa->wsas_path . '">
                                            <inpdomain>' . $domain . '</inpdomain>
                                        </php_forecast>
                                    </Body>
                                </Envelope>';


                $curlOptions = array(
                    CURLOPT_URL => $wsa_url,
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

                $xmlResp = simplexml_load_string($qdocResponse);

                $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);

                $dataloop   = $xmlResp->xpath('//ns1:tempRow');
                $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

                if ($qdocResult == 'true') {
                    foreach ($dataloop as $dataWSA) {
                        $item = ItemMRP::where('item_code', $dataWSA->t_mrp_part)->first();
                        if (!$item) {
                            DB::rollBack();
                            $errMessage = 'Item MRP: ' . $dataWSA->t_mrp_part . ' tidak terdapat pada table item (Forecast)' . PHP_EOL;
                            Log::channel('loadRencanaProduksi')->info($errMessage);
                        } else {
                            $dataRencanaProduksi = new ForecastRencanaProduksi();
                            $dataRencanaProduksi->id_rp_item = $item->id;
                            $dataRencanaProduksi->isForecast = 1;
                            $dataRencanaProduksi->rp_mrp_nbr = $dataWSA->t_mrp_nbr;
                            $dataRencanaProduksi->rp_mrp_bulan = $dataWSA->t_mrp_bulan;
                            $dataRencanaProduksi->rp_mrp_tahun = $dataWSA->t_mrp_tahun;
                            $dataRencanaProduksi->rp_mrp_dataset = $dataWSA->t_mrp_dataset;
                            $dataRencanaProduksi->rp_mrp_type = $dataWSA->t_mrp_type;
                            $dataRencanaProduksi->rp_mrp_due_date = (string)$dataWSA->t_mrp_due_date;
                            $dataRencanaProduksi->rp_mrp_qty = $dataWSA->t_mrp_qty;
                            $dataRencanaProduksi->save();
                        }
                    }
                } else {
                    DB::rollBack();
                    $errMessage = 'WSA get forecast returns false' . PHP_EOL;
                    Log::channel('loadRencanaProduksi')->info($errMessage);
                }

                // Wsa ambil rencana produksi
                $qdocRequest = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                    <Body>
                                        <php_rencanaProduksi xmlns="' . $wsa->wsas_path . '">
                                            <inpdomain>' . $domain . '</inpdomain>
                                        </php_rencanaProduksi>
                                    </Body>
                                </Envelope>';


                $curlOptions = array(
                    CURLOPT_URL => $wsa_url,
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

                $xmlResp = simplexml_load_string($qdocResponse);

                $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);

                $dataloop   = $xmlResp->xpath('//ns1:tempRow');
                $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

                // if ($qdocResult == 'true') {
                //     foreach ($dataloop as $dataWSA) {
                //         $item = ItemMRP::where('item_code', $dataWSA->t_mrp_part)->first();
                //         if (!$item) {
                //             DB::rollBack();
                //             $errMessage = 'Item MRP: ' . $dataWSA->t_mrp_part . ' tidak terdapat pada table item (Rencana Produksi)' . PHP_EOL;
                //             Log::channel('loadRencanaProduksi')->info($errMessage);
                //         } else {
                //             $dataRencanaProduksi = new ForecastRencanaProduksi();
                //             $dataRencanaProduksi->id_rp_item = $item->id;
                //             $dataRencanaProduksi->isForecast = 0;
                //             $dataRencanaProduksi->rp_mrp_nbr = $dataWSA->t_mrp_nbr;
                //             $dataRencanaProduksi->rp_mrp_bulan = $dataWSA->t_mrp_bulan;
                //             $dataRencanaProduksi->rp_mrp_tahun = $dataWSA->t_mrp_tahun;
                //             $dataRencanaProduksi->rp_mrp_dataset = $dataWSA->t_mrp_dataset;
                //             $dataRencanaProduksi->rp_mrp_type = $dataWSA->t_mrp_type;
                //             $dataRencanaProduksi->rp_mrp_due_date = (String)$dataWSA->t_mrp_due_date;
                //             $dataRencanaProduksi->rp_mrp_qty = $dataWSA->t_mrp_qty;
                //             $dataRencanaProduksi->save();
                //         }
                //     }
                // } else {
                //     DB::rollBack();
                //     $errMessage = 'WSA get Rencana Produksi returns false' . PHP_EOL;
                //     Log::channel('loadRencanaProduksi')->info($errMessage);
                // }

                if ($qdocResult == 'true') {
                    foreach ($dataloop as $dataWSA) {
                        $item = ItemMRP::where('item_code', $dataWSA->t_mrp_part)->first();
                        if ($item) {
                            $dataRencanaProduksi = new ForecastRencanaProduksi();
                            $dataRencanaProduksi->id_rp_item = $item->id;
                            $dataRencanaProduksi->isForecast = 0;
                            $dataRencanaProduksi->rp_mrp_nbr = $dataWSA->t_mrp_nbr;
                            $dataRencanaProduksi->rp_mrp_bulan = $dataWSA->t_mrp_bulan;
                            $dataRencanaProduksi->rp_mrp_tahun = $dataWSA->t_mrp_tahun;
                            $dataRencanaProduksi->rp_mrp_dataset = $dataWSA->t_mrp_dataset;
                            $dataRencanaProduksi->rp_mrp_type = $dataWSA->t_mrp_type;
                            $dataRencanaProduksi->rp_mrp_due_date = (string)$dataWSA->t_mrp_due_date;
                            $dataRencanaProduksi->rp_mrp_qty = $dataWSA->t_mrp_qty;
                            $dataRencanaProduksi->save();
                        }
                    }
                } else {
                    // DB::rollBack();
                    $errMessage = 'Tidak ada rencana produksi' . PHP_EOL;
                    Log::channel('loadRencanaProduksi')->info($errMessage);
                }

                DB::commit();
            } else {
                DB::rollBack();
                $errMessage = 'WSA get item returns false' . PHP_EOL;
                Log::channel('loadRencanaProduksi')->info($errMessage);
            }
        } catch (\Exception $err) {
            DB::rollBack();
            Log::channel('loadRencanaProduksi')->info($err);
        }
    }
}
