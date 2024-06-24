<?php
session_start();
include("../connect.php");
include("../config.php");
include('pdf.php');
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

$result = $db->prepare('SELECT * FROM customer WHERE  customer_id=:id ');
$result->bindParam(':id', $_GET['id']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['customer_name'];
    $vat_no = $row['vat_no'];
    $address = $row['address'];
    $contact = $row['whatsapp'];
}

$vat_action = 0;

if (strlen($vat_no) > 0) {
    $vat_action = 1;
}


$in_type = "INVOICE";

if ($vat_action == 1) {
    $in_type = "TAX INVOICE";
}

$id = $_GET['id'];

$amount = 0;
$payment = 0;
$credit_list = "";
$result = $db->prepare("SELECT * FROM payment WHERE action='2' and type='credit' and credit_balance>0 and customer_id=:id ");
$result->bindParam(":id", $_GET['id']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {


    $amount += $row['amount'];
    $payment += $row['pay_amount'];

    $credit_list .= '
                 <tr>
                    <td style="border-bottom: 1px solid #ccc;">' . $row['invoice_no'] . '</td>
                    <td style="border-bottom: 1px solid #ccc;">' . $row["date"] . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row['amount'], 2) . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row["pay_amount"], 2) . '</td>
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
                    <td colspan="2" valign="top" width="100%" align="center">
                        <img src="../icon/Logo-Laugfs-Gas.png" alt="Logo" style="max-width:200px;"><br>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="60%">
                         <b style="font-family: Poppins; font-size:17px">' . $info_name . '</b><br>
                         <b style="font-family: Poppins; font-size:15px">' . $info_add . '</b><br>
                         <b style="font-family: Poppins; font-size:15px">' . $info_con . '</b><br>
     					 <b style="font-size:15px">VAT No: </b> ' . $info_vat . '<br><br><br>
                    </td>
                    <td align="right" valign="top" width="40%">
                        <b style="font-family:Poppins; font-size:30px">' . $in_type . '</b><br><br>
                        <b style="font-family: Poppins; font-size:14px"> ' . $name . '</b><br>
                        <b style="font-family: Poppins; font-size:14px">' . $address . '</b><br>
                        <p>Date: ' . date('Y-M-d') . ' Time:' . date('H:m') . '</p><br><br><br>
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
                    <th style="border-bottom: 1px solid #ccc;">Invoice</th>
                    <th style="border-bottom: 1px solid #ccc;">Date</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Amount</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Payment</th>
                </tr>

                ' . $credit_list . '
            </table>
        </td>
    </tr>
    
    <tr>
        <td>
        <br><br><br>
        </td>
    </tr>

    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="font-size:20px" colspan="4" align="right"><h3>Total:</h3></td>
                    <td style="font-size:20px" align="right"><h3>Rs.' . number_format($amount, 2) . '</h3></td>
                </tr>
                <tr>
                    <td align="center"><img src="../icon/r.png" width="40" alt=""></td>
                    <td style="font-size:18px" colspan="3" align="right">Pay Amount:</td>
                    <td style="font-size:18px" align="right">Rs.' . number_format($payment, 2) . '</td>
                </tr>
                <tr>
                    <td align="center">CLOUD ARM</td>
                    <td style="font-size:18px" colspan="3" align="right">Balance:</td>
                    <td style="font-size:18px" align="right">Rs.' . number_format($amount - $payment, 2) . '</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
';


// $contact = '94762020312';
if (!empty($contact)) {
    $text = 'This is a system generated document ...';
    $url = get_pdf($output, 'credit', 'bin/');
    whatsApp($contact, $text, $url);
}

$return = $_SESSION['SESS_BACK'];

header("location: ../$return");
