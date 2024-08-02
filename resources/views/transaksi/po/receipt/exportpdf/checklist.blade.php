<!DOCTYPE html>
<html>

<head>
    <title>DAFTAR PERIKSA PENERIMAAN BAHAN / BARANG</title>
</head>

<style>
    @page {
        margin: 6mm 9mm 6mm 9mm;
        border-collapse: collapse;
    }


    body {
        font-size: 12px;
        font-family: 'Calibri', Helvetica, sans-serif;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    tbody {
        border: 1px solid black;
    }

    table tr td {
        /* border: 1px solid black; */
        vertical-align: middle;
        line-height: 26px;
        padding-left: 5px;
        padding-right: 5px;
        margin: 0;
    }

    .noborder {
        border: none;
    }

    .innertable tr td {
        border-collapse: collapse;
        text-align: center;
    }

    .botborder {
        border-bottom: 1px solid black;
    }

    .rightborder {
        border-right: 1px solid black;
    }

    .allborder tr td {
        border: 1px solid black;
    }

    .head50 {
        width: 50%;
        display: inline-block;
        vertical-align: top;
    }

    .head40 {
        width: 45%;
        display: inline-block;
        vertical-align: top;
        text-align: right;
    }

    .head30 {
        width: 33%;
        display: inline-block;
        vertical-align: top;
        text-align: center;
    }

    .judul {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        vertical-align: middle;
        line-height: 30px;
    }

    .textright {
        text-align: right;
    }

    .textcenter {
        text-align: center;
    }
</style>

<body>
    @php($flg = 0)
    <hr style="margin-bottom: 15px;">
    <div class="head30">
        <table>
            <tbody>
                <tr>
                    <td width="20%" style="text-align: right">No.</td>
                    <td width="40%">: <b></b> </td>
                </tr>
                <tr>
                    <td width="20%" style="text-align: right">Revisi</td>
                    <td width="40%">:</td>
                </tr>
                <tr>
                    <td width="20%" style="text-align: right">Efektif Date</td>
                    <td width="40%">:</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="head30">
        <table>
            <tbody class="noborder">
                <tr>
                    <td width="20%" style="text-align: center" rowspan="3" colspan="3">
                        <h3>DAFTAR PERIKSA PENERIMAAN BAHAN / BARANG</h3>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="head30">
        <img src="{{ public_path('images/logo_php.jpg') }}" alt="" height="100" width="200">
    </div>
    <hr style="margin-bottom: 15px; margin-top: 15px;">

    <table class="allborder">
        {{-- Header --}}
        <tr>
            <td width="30%">No BPB / IMRN No.</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_imr_nbr }}
            </td>
            <td width="25%">Tanggal BPB / IMR Date</td>
            <td width="20%">
                {{ $data->getChecklist->rcptc_imr_date }}
            </td>
        </tr>
        <tr>
            <td width="25%">Tgl Datang / Arrival Date.</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_arrival_date }}
            </td>
            <td width="25%">No. PO / PO No.</td>
            <td width="25%">

            </td>
        </tr>
        <tr>
            <td width="25%">Kode Bahan/Barang / Item No.</td>
            <td width="25%">
                {{ $data->getDetail[0]->rcptd_part ?? '' }}
            </td>
            <td width="25%">No. DO / DO No.</td>
            <td width="25%">

            </td>
        </tr>
        <tr>
            <td width="25%" rowspan="2"><b>Nama Bahan / Item Name :</b></td>
            <td width="20%" rowspan="2">
                {{ $data->getDetail[0]->rcptd_part_desc ?? '' }}
            </td>
            <td width="25%">Article No.</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_article_nbr }}
            </td>
        </tr>
        <tr>
            <td width="25%">Pemasok / Supplier.</td>
            <td width="25%">
                {{ $data->getpo->po_vend_desc }}
            </td>
        </tr>
        <tr>
            <td width="25%">Pabrik Pembuat / Manufacturer.</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_manufacturer }}
            </td>
            <td width="25%">Prod. / Manuf. Date</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_prod_date }}
            </td>
        </tr>
        <tr>
            <td width="25%">Negara Asal / Country Origin</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_country }}
            </td>
            <td width="25%">Exp. Date</td>
            <td width="25%">
                {{ $data->getChecklist->rcptc_exp_date }}
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <b>*) Untuk BB, pastikan ED yang diterima min.1th</b>
                <br>
                <b>*) Untuk BK tanpa ED, penentuan ED mengikuti prosedur yang berlaku</b>
            </td>
        </tr>
        {{-- Looping Data Line --}}
        @foreach ($data->getDetail as $key => $row)
            <tr>
                <td>
                    {{-- Line Update --}}
                    {{$key + 1}}. No. Batch / Lot
                </td>
                <td colspan="3">
                    {{-- Nomor Batch --}}
                    {{$row->rcptd_batch}}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">.... containers @ {{$row->rcptd_qty_per_package}} units</td>
            </tr>
            <tr>
                <td>Jumlah datang / Received Qty</td>
                <td colspan="2">
                    {{-- Qty Datang & UM --}}
                    {{$row->rcptd_qty_arr}} &nbsp; {{$row->rcptd_part_um}}
                </td>
                <td>
                    (A)
                </td>
            </tr>
            <tr>
                <td>Jumlah ditolak / Rejected Qty</td>
                <td colspan="2">
                    {{-- Qty Tolak & UM --}}
                    {{$row->rcptd_qty_rej}} &nbsp; {{$row->rcptd_part_um}}
                </td>
                <td>
                    (B)
                </td>
            </tr>
            <tr>
                <td>Jumlah diterima / Accepted Qty</td>
                <td colspan="2">
                    {{-- Qty Terima & UM --}}
                    {{(int)$row->rcptd_qty_appr}} &nbsp; {{$row->rcptd_part_um}}
                </td>
                <td>
                    (C = A-B)
                </td>
            </tr>
        @endforeach

        {{-- Kelengkapan Dokumen --}}
        <tr>
            <td colspan="4" style="text-align: center;font-size:16px;"><b>Kelengkapan Dokumen</b> / Completeness of
                Documents</td>
        </tr>
        <tr>
            <td></td>
            <td>Ada / Available</td>
            <td>Tidak Ada / Not Available</td>
            <td>Keterangan</td>
        </tr>
        <tr>
            <td>1. Certificate of Analysis</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_certofanalys == 1 ? 'X' : '' }}</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_certofanalys == 0 ? 'X' : '' }}</td>
            <td>{{ $data->getDocument->rcptdoc_certofanalys }} </td>
        </tr>
        <tr>
            <td>2. MSDS</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_msds == 1 ? 'X' : '' }}</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_msds == 0 ? 'X' : '' }}</td>
            <td>{{ $data->getDocument->rcptdoc_msds }}</td>
        </tr>
        <tr>
            <td>3. Surat Jalan Angkutan / Forwarder DO</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_forwarderdo == 1 ? 'X' : '' }}</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_forwarderdo == 0 ? 'X' : '' }}</td>
            <td>{{ $data->getDocument->rcptdoc_forwarderdo }}</td>
        </tr>
        <tr>
            <td>4. Packing List</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_packinglist == 1 ? 'X' : '' }}</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_packinglist == 0 ? 'X' : '' }}</td>
            <td>{{ $data->getDocument->rcptdoc_packinglist }}</td>
        </tr>
        <tr>
            <td>5. Lain-lain / Other docs.</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_otherdocs == 1 ? 'X' : '' }}</td>
            <td style="text-align: center">{{ $data->getDocument->rcptdoc_is_otherdocs == 0 ? 'X' : '' }}</td>
            <td>{{ $data->getDocument->rcptdoc_otherdocs }}</td>
        </tr>
        {{-- Kondisi Kemasan --}}
        <tr>
            <td colspan="3" style="text-align: center;font-size:16px;"><b>Kondisi Kemasan</b> / Container Condition
            </td>
            <td>Keterangan / Remark</td>
        </tr>
        <tr>
            <td>Kemasan Sack / Dos</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_kemasan_sacdos == 1 && $data->getKemasan->rcptk_kemasan_sacdos_desc == 'Undamage' ? 'checked' : '' }}>
                    Tidak Sobek / Intact</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_kemasan_sacdos == 1 && $data->getKemasan->rcptk_kemasan_sacdos_desc == 'Damage' ? 'checked' : '' }}>
                    Sobek / Torn</label>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>Kemasan Drum / Vat</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_kemasan_drumvat == 1 && $data->getKemasan->rcptk_kemasan_drumvat_desc == 'Undamage' ? 'checked' : '' }}>
                    Tidak Rusak / undamage</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_kemasan_drumvat == 1 && $data->getKemasan->rcptk_kemasan_drumvat_desc == 'Damage' ? 'checked' : '' }}>
                    Rusak / damage</label>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>Kemasan Pallet / Peti</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_kemasan_palletpeti == 1 && $data->getKemasan->rcptk_kemasan_palletpeti_desc == 'Undamage' ? 'checked' : '' }}>
                    Tidak Rusak / undamage</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_kemasan_palletpeti == 1 && $data->getKemasan->rcptk_kemasan_palletpeti_desc == 'Damage' ? 'checked' : '' }}>
                    Rusak / damage</label>
            </td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="3">Keadaan / Condition</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_clean == 1 ? 'Checked' : '' }}> Bersih / Clean</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_clean == 0 ? 'Checked' : '' }}> Kotor / Dirty</label>
            </td>
            <td>
                <label for="">{{ $data->getKemasan->rcptk_is_clean_desc }}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_dry == 1 ? 'Checked' : '' }}> Kering / Dry</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_dry == 0 ? 'Checked' : '' }}> Basah / Wet</label>
            </td>
            <td>
                <label for=""> {{ $data->getKemasan->rcptk_is_dry_desc }}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_not_spilled == 1 ? 'Checked' : '' }}> Tidak Bocor / Not
                    Spilled</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_not_spilled == 0 ? 'Checked' : '' }}> Bocor / Spilled</label>
            </td>
            <td>
                <label for="">{{ $data->getKemasan->rcptk_is_not_spilled_desc }}</label>
            </td>
        </tr>
        <tr>
            <td>Segel / Seal</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_sealed == 1 ? 'Checked' : '' }}> Utuh / Intact</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_sealed == 0 ? 'Checked' : '' }}> Rusak / Broken</label>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>Label Pabrik / Manufacturer Label</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_manufacturer_label == 1 ? 'Checked' : '' }}> Ada /
                    Available</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_is_manufacturer_label == 0 ? 'Checked' : '' }}> Tidak Ada / Not
                    Available</label>
            </td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2">*) Khusus untuk Bahan Halal, cek logo HALAL / Check halal sign</td>
            <td>
                <label for=""><input type="checkbox" 
                        {{ $data->getKemasan->rcptk_has_logo_halal == 1 ? 'Checked' : '' }}> Ada / Available</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getKemasan->rcptk_no_logo_halal == 1 ? 'Checked' : '' }}> Tidak Ada / Not Available</label>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                <label for=""><input type="checkbox"
                    {{ $data->getKemasan->rcptk_not_regulated_logo_halal == 1 ? 'Checked' : '' }}> Tidak Diterapkan / Not Applicable</label>
            </td>
            <td colspan="2">
                <b>*) Berikan penandaan Not Applicable apabila bahan/barang yang diterima tidak termasuk dalam Daftar
                    Bahan/Barang Halal</b>
            </td>
        </tr>
        {{-- Angkutan --}}
        <tr>
            <td colspan="4" style="font-size: 16px;text-align:center;"><b>Kondisi Angkutan</b> / Transport
                Condition</td>
        </tr>
        <tr>
            <td>Nama Angkutan / Transporter No.</td>
            <td colspan="3">
                {{ $data->getTransport->rcptt_transporter_no }}
            </td>
        </tr>
        <tr>
            <td>No. Polisi / Police No.</td>
            <td colspan="3">
                {{ $data->getTransport->rcptt_police_no }}
            </td>
        </tr>
        <tr>
            <td rowspan="3">Keadaan Angkutan / Transporter Condition</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_clean == 1 ? 'Checked' : '' }}> Bersih / Clean</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_clean == 0 ? 'Checked' : '' }}> Kotor / Dirty</label>
            </td>
            <td>
                <label for=""> {{ $data->getTransport->rcptt_is_clean_desc }}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_dry == 1 ? 'Checked' : '' }}> Kering / Dry</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_dry == 0 ? 'Checked' : '' }}> Basah / Wet</label>
            </td>
            <td>
                <label for=""> {{ $data->getTransport->rcptt_is_dry_desc }}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_not_spilled == 1 ? 'Checked' : '' }}> Tidak Bocor / Not
                    Spilled</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_not_spilled == 0 ? 'Checked' : '' }}> Bocor / Spilled</label>
            </td>
            <td>
                <label for="">{{ $data->getTransport->rcptt_is_not_spilled_desc }}</label>
            </td>
        </tr>
        <tr>
            <td rowspan="2">Penempatan bahan/barang dalam angkutan / Material Position</td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_position_single == 1 ? 'Checked' : '' }}> Tunggal /
                    Single</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_position_single == 0 ? 'Checked' : '' }}> Campuran /
                    Combination</label>
            </td>
            <td> {{ $data->getTransport->rcptt_is_position_single_desc }}</td>
        </tr>
        <tr>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_segregated == 1 ? 'Checked' : '' }}> Ada pemisahan antara
                    bahan/barang / Clear Segregation</label>
            </td>
            <td>
                <label for=""><input type="checkbox"
                        {{ $data->getTransport->rcptt_is_segregated == 0 ? 'Checked' : '' }}> Tanpa pemisahan antara
                    bahan/barang / No Segregation</label>
            </td>
            <td> {{ $data->getTransport->rcptt_is_segregated_desc }}</td>
        </tr>
        <tr>
            <td colspan="4">
                Catatan Remarks
            </td>
        </tr>
        <tr>
            <td colspan="4" style="line-height: 70px;">
                {{-- Catatan  --}}
                {{ $data->getTransport->rcptt_angkutan_catatan }}
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <b>*) Untuk barang yang sudah diterima oleh user, berikan keterangan : </b>
                <br>
                Barang Diterima oleh : {{ $data->getUser->nama ?? '' }}
                <br>
                Penerima Barang :
                <br>
                <br>
                <br>
                <p style="text-align: center !important">
                    Nama Jelas dan paraf / Name & Sign
                    <br>
                    <b>{{ $data->getUser->nama ?? '' }}</b>

                </p>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="margin: 0px !important; padding: 0px !important;">
                <table class="innertable">
                    <tr>
                        <td width="33%">Diterima Oleh / Received By</td>
                        <td width="33%">Diperiksa Oleh / Checked By</td>
                        <td width="33%">Diketahui Oleh / Aknowledge By</td>
                    </tr>
                    <tr>
                        <td style="line-height: 100px;">.</td>
                        <td style="line-height: 100px;"></td>
                        <td style="line-height: 100px;"></td>
                    </tr>
                    <tr>
                        <td>
                            Nama jelas dan paraf / Name & Sign
                            <br>
                            <b>Petugas Penerimaan Barang/Bahan / Material Warehouse Personel</b>
                        </td>
                        <td>
                            Nama jelas dan paraf / Name & Sign
                            <br>
                            <b>Koordinator/Supervisor/Apoteker/Gudang Bahan/ Material Warehouse
                                Coordinator/Supervisor/Pharmacist</b>
                        </td>
                        <td>
                            Nama jelas dan paraf / Name & Sign
                            <br>
                            <b>Pengirim Barang / Sender</b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($flg > 7)
        <div style='page-break-before:always'></div>
    @endif
</body>

</html>
