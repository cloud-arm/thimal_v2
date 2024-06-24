<?php

function send_invoice($db, $id, $path = '')
{
    date_default_timezone_set("Asia/Colombo");
    $date = date("Y-m-d");
    $time = date("H.i");

    $result = $db->prepare("SELECT * FROM info ");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $info_name = $row['name'];
        $info_add = $row['address'];
        $info_vat = $row['vat_no'];
        $info_con = $row['phone_no'];
    }

    $sql = "SELECT * FROM sales WHERE invoice_number='$id'  ";
    $result1 = $db->prepare($sql);
    $result1->bindParam(':userid', $date);
    $result1->execute();
    for ($i = 0; $row1 = $result1->fetch(); $i++) {

        $vat_action = 0;
        $result = $db->prepare('SELECT * FROM customer WHERE  customer_id=:id ');
        $result->bindParam(':id', $row1['customer_id']);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $name = $row['customer_name'];
            $vat_no = $row['vat_no'];
            $address = $row['address'];
            $contact = $row['whatsapp'];
        }
        if (strlen($vat_no) > 0) {
            $vat_action = 1;
        }

        $total = $row1["amount"];

        if ($vat_action == 1) {

            $in_type = "TAX INVOICE";

            $total = ($total / 118) * 100;
            $vat = ($total / 118) * 18;
        } else {
            $in_type = "INVOICE";
        }

        $tot_row = '
                <tr>
                    <td align="center"><img src="' . $path . 'icon/r.png" width="40" alt=""></td>
                    <td style="font-size:18px" colspan="3" align="right"><h3>Total:</h3></td>
                    <td style="font-size:18px" align="right"><h3>Rs.' . number_format($total, 2) . '</h3></td>
                </tr>
                <tr>
                    <td align="center">CLOUD ARM</td>
                    <td style="font-size:18px" colspan="3" align="right"><h3></h3></td>
                    <td style="font-size:18px" align="right"><h3></h3></td>
                </tr>
                ';

        if ($vat_action == 1) {
            $tot_row = '
                <tr>
                    <td align="center"><img src="' . $path . 'icon/r.png" width="40" alt=""></td>
                    <td style="font-size:18px" colspan="3" align="right"><h3>Total:</h3></td>
                    <td style="font-size:18px" align="right"><h3>Rs.' . number_format($total, 2) . '</h3></td>
                </tr>
                <tr>
                    <td align="center">CLOUD ARM</td>
                    <td style="font-size:18px" colspan="3" align="right"><h3>VAT:</h3></td>
                    <td style="font-size:18px" align="right"><h3>Rs.' . number_format($vat, 2) . '</h3></td>
                </tr>
                ';
        }


        $sales_list = "";
        $result = $db->prepare("SELECT * FROM sales_list WHERE  invoice_no=:id ");
        $result->bindParam(":id", $row1['invoice_number']);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {

            $price = $row['price'] - ($row['dic'] / $row['qty']);
            $amount = $price * $row['qty'];

            if ($vat_action == 1) {
                $price = ($price / 118) * 100;
                $amount = ($amount / 118) * 100;
            }

            $sales_list .= '
                 <tr>
                    <td style="border-bottom: 1px solid #ccc;">' . ($i + 1) . '</td>
                    <td style="border-bottom: 1px solid #ccc;">' . $row["name"] . '</td>
                    <td align="center" style="border-bottom: 1px solid #ccc;">' . $row["qty"] . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($price, 2) . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($amount, 2) . '</td>
                 </tr> ';
        }

        $output = '
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<style>
body {
font-family: Poppins;
}
</style>
</head>
<body>
<table style="font-size: 12px;"  cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td>
            <table  cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td valign="top" width="60%">
                        <img src="' . $path . 'icon/Logo-Laugfs-Gas.png" alt="Logo" style="max-width:150px;"><br>
                         <b style="font-family: Poppins; font-size:17px">' . $info_name . '</b><br>
                         <b style="font-family: Poppins; font-size:15px">' . $info_add . '</b><br>
                         <b style="font-family: Poppins; font-size:15px">' . $info_con . '</b><br>
     					 <b style="font-size:15px">VAT No: </b> ' . $info_vat . '<br><br><br>
                    </td>
                    <td align="right" valign="top" width="40%">
                        <b style="font-family:Poppins; font-size:30px">' . $in_type . '</b><br><br>
                        <b style="font-family: Poppins; font-size:14px"> ' . $name . '</b><br>
                        <b style="font-family: Poppins; font-size:14px">' . $address . '</b><br>
                        <b style="font-family: Poppins; font-size:14px">#' . $row1['invoice_number'] . '</b><br>
                        <p>Date: ' . date('Y-M-d') . ' Time:' . date('H:m') . '</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td>

        </td>
    </tr>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <th style="border-bottom: 1px solid #ccc;">ID</th>
                    <th style="border-bottom: 1px solid #ccc;">Description</th>
                    <th style="border-bottom: 1px solid #ccc;">Quantity</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Unit Price</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Total</th>
                </tr>

                ' . $sales_list . '

                <tr>
                    <td colspan="4" align="right"><h3><br></h3></td>
                    <td align="right"><h3><br></h3></td>
                </tr>

                ' . $tot_row . '
            </table>
        </td>
    </tr>
</table>
</body>
</html>
';
    }

    $contact = '94762020312';
    if (!empty($contact)) {
        $text = 'This is a system generated document ...';
        $url = get_pdf($output, 'invoice', 'pdf/bin/');
        whatsApp($contact, $text, $url);
    }
}
