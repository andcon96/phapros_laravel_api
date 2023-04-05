<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Sending Email</title>
    <style type="text/css">
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman';
            padding: 12px;
            background: #f1f1f1;
        }

        /* Header/Blog Title */
        .header {
            padding: 12px;
            text-align: center;
            background: white;
        }

        .header h1 {
            font-size: 34px;
        }

        /* Create two unequal columns that floats next to each other */
        /* Left column */
        .leftcolumn {
            float: left;
            width: 100%;
            padding-left: 10%;
            padding-right: 10%;
        }

        /* Right column */
        .rightcolumn {
            float: left;
            width: 25%;
            background-color: #f1f1f1;
            padding-left: 20px;
        }

        /* Fake image */
        .fakeimg {
            background-color: #aaa;
            width: 100%;
            padding: 20px;
        }

        /* Add a card effect for articles */
        .card {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Footer */
        .footer {
            padding: 10px;
            text-align: center;
            background: #ddd;
            margin-top: 20px;
        }

        .button {
            background-color: #E4D00A;
            border: none;
            color: white;
            padding: 16px 32px;
            text-align: center;
            font-size: 14px;
            margin: 4px 2px;
            opacity: 0.6;
            transition: 0.3s;
            display: inline-block;
            text-decoration: none;
            cursor: pointer;
        }

        .button2 {
            background-color: #EE4B2B;
            border: none;
            color: white;
            padding: 16px 32px;
            text-align: center;
            font-size: 14px;
            margin: 4px 2px;
            opacity: 0.6;
            transition: 0.3s;
            display: inline-block;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover {
            opacity: .8
        }

        .button2:hover {
            opacity: .8
        }

        table,
        tr,
        td {
            margin: 0 auto;
            border: 1px solid black;
        }

        /* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other */
        @media screen and (max-width: 800px) {

            .leftcolumn,
            .rightcolumn {
                width: 100%;
                padding: 0;
            }

            .header {
                font-size: 14px;
            }

            .header h1 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body class="">
    <div class="header" style="text-align: left;">
        <h5>Dear Sir/ Madam,</h5>
        <br>
       
        <h5>There's new Purchase Order that needs your review and approval, which following detail:</h5>
       
        <div>
            <table style="width:100%">
                <thead style="font-size:12px;font-family: 'Times New Roman', Times, serif">
                    <th style="background-color: black; color:white">PO No.</th>
                    <th style="background-color: black; color:white">Receipt No.</th>
                    <th style="background-color: black; color:white">Receipt Date</th>
                    <th style="background-color: black; color:white">Receipt User</th>
                </thead>
                <tbody style="font-size:12px;font-family: 'Times New Roman', Times, serif">
                    <tr>
                        <td>
                            {{ $ponbr }}
                        </td>
                        <td>
                            {{ $receiptnbr }}
                        </td>
                        <td>
                            {{ date('d M Y', strtotime($datareceipt->rcpt_date)) }}
                        </td>
                        <td>
                            {{ $nik }} -- {{ $nama }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div>
            <table style="width:100%">
                <thead style="font-size:12px;font-family: 'Times New Roman', Times, serif">
                    <th style="background-color: black; color:white">No</th>
                    <th style="background-color: black; color:white">Item Number</th>
                    <th style="background-color: black; color:white">item Description</th>
                    <th style="background-color: black; color:white">Qty Datang</th>
                    <th style="background-color: black; color:white">Qty Terima</th>
                    <th style="background-color: black; color:white">Qty Reject</th>
                </thead>
                <tbody style="font-size:12px;font-family: 'Times New Roman', Times, serif">
                    @foreach ($detailreceipt as $key => $detail)
                        <tr>
                            <td>
                                {{ $key + 1 }}
                            </td>
                            <td>
                                {{ $detail->rcptd_part }}
                            </td>
                            <td>
                                {{ $detail->rcptd_part_desc }}
                            </td>
                            <td>
                                {{ $detail->rcptd_qty_arr }}
                            </td>
                            <td>
                                {{ $detail->rcptd_qty_appr }}
                            </td>
                            <td>
                                {{ $detail->rcptd_qty_rej }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <div style="text-align: left;">
            <p>Best Regards, </p>
            <p>QAD Application </p>
            <h6>This is an auto generated email. Please do not reply to this address </h6>
        </div>
    </div>

    {{-- <div class="footer" style="text-align: center;">
        <h5>@PT Intelegensia Mustaka Indonesia - 2022</h5>
    </div> --}}

</body>

</html>
