<!DOCTYPE html>
<html>

<head>
    <title>LAPORAN KETIDAKSESUAIAN PENERIMAAN BAHAN/BARANG</title>
</head>

<style>
    @page {
        margin: 6mm 15mm 6mm 15mm;
        border-collapse: collapse;
    }


    body {
        font-size: 13px;
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

    .botborder {
        border-bottom: 1px solid black;
    }

    .rightborder {
        border-right: 1px solid black;
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

    @foreach ($data as $key => $row)
        <div class="head50">
            <table>
                <tbody class="noborder">
                    <tr>
                        <td width="20%" style="text-align: right">No. Formulir</td>
                        <td width="40%">: <b></b> </td>
                    </tr>
                    <tr>
                        <td width="20%" style="text-align: right">Tanggal Efektif</td>
                        <td width="40%">: <b>{{ \Carbon\Carbon::now()->toDateString() }}</b> </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="head40">
            <img src="{{ public_path('images/logo_php.jpg') }}" alt="" height="100" width="200">
        </div>

        <table style="margin-top:20px;">

            <body>
                <tr>
                    <td colspan="4" class="judul">LAPORAN KETIDAKSESUAIAN PENERIMAAN BAHAN/BARANG</td>
                </tr>
                <tr class="botborder">
                    <td width="35%" class="textright">No :</td>
                    <td width="20%"></td>
                    <td width="20%">Tgl :</td>
                    <td></td>
                </tr>
            </body>
        </table>
        <table>

            <body>
                <tr>
                    <td width="15%">Supplier</td>
                    <td class="botborder" colspan="4">{{ $row->po_vend_desc }}</td>
                    <td width="25%"></td>
                </tr>
                <tr>
                    <td width="15%">Komplain</td>
                    <td class="botborder" colspan="4">{{ $row->laporan_komplain }}</td>
                    <td class="botborder"></td>
                </tr>
                <tr style="">
                    <td width="15%" style="line-height: 60px;vertical-align: top !important;">Ket</td>
                    <td class="botborder rightborder" colspan="4"  style="line-height: 60px;vertical-align: top !important;"> {{ $row->laporan_keterangan }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td width="15%">Nama Barang</td>
                    <td class="botborder rightborder" colspan="4">{{ $row->laporan_part }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td width="15%">Tgl Masuk</td>
                    <td class="botborder rightborder">{{ $row->laporan_tgl_masuk }}</td>
                    <td width="15%" class="textcenter">PO</td>
                    <td class="botborder rightborder">{{ $row->po_nbr }}</td>
                    <td width="15%" class="textcenter">No Lot</td>
                    <td class="botborder">{{ $row->laporan_lot }}</td>
                </tr>
                <tr>
                    <td width="15%">Jml Masuk</td>
                    <td class="botborder">{{ $row->laporan_jmlmasuk }}</td>
                    <td width="15%" class="textcenter">Sat</td>
                    <td class="botborder rightborder">{{ $row->rcptd_part_um }}</td>
                    <td width="15%" class="textcenter">Angkutan</td>
                    <td class="botborder">{{ $row->laporan_angkutan }}</td>
                </tr>
                <tr>
                    <td width="15%">Komplain</td>
                    <td class="botborder">{{ $row->laporan_komplaindetail }}</td>
                    <td></td>
                    <td></td>
                    <td width="15%" class="textcenter">No. Pol.</td>
                    <td class="botborder">{{ $row->laporan_nopol }}</td>
                </tr>
                <tr>
                    <td colspan="6" style="line-height: 10px"> .</td>
                </tr>
            </body>
        </table>
        <table>
            <tbody>
                <tr>
                    <td class="botborder rightborder" style="width:50%" colspan="2">Semarang, {{\Carbon\Carbon::now()->format('d M Y')}}</td>

                    <td>Mengetahui,</td>
                </tr>
                <tr>
                    <td style="line-height: 100px;" class="rightborder botborder">.

                    </td>
                    <td class="rightborder botborder" style="text-align: center">
                        @if(file_exists(public_path($row->rcptfu_path)))
                        <img src="{{public_path($row->rcptfu_path)}}" height="80" width="80" alt="">
                        @endif
                    </td>
                    <td class="botborder"></td>
                </tr>
                <tr>
                    <td class="rightborder">Petugas.Gudang Bahan</td>
                    <td class="rightborder">Transporter</td>
                    <td>Ass.Man. Gudang Bahan</td>
                </tr>
            </tbody>
        </table>

        @if ($key + 1 != count($data))
            <div style='page-break-before:always'></div>
        @endif
    @endforeach
</body>

</html>
