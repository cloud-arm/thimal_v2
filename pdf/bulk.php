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

$result = $db->prepare("SELECT * FROM payment WHERE  transaction_id=:id  ");
$result->bindParam(':id', $_GET['id']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $invo = $row['invoice_no'];
    $invo_date = $row['date'];
    $type = $row['type'];
    $amount = $row['amount'];
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $bank = $row['chq_bank'];
}

$chq_details = '';
if ($type == 'chq' | $type == 'Chq') {
    $chq_details .= '<b style="font-family: Poppins; font-size:14px"> CHQ No: ' . $chq_no . '</b><br>';
    $chq_details .= '<b style="font-family: Poppins; font-size:14px"> CHQ Date: ' . $chq_date . '</b><br>';
}

$in_type = "PAYMENT RECEIPT";

$id = $_GET['id'];

$bulk_list = "";
$result = $db->prepare("SELECT * FROM credit_payment WHERE pay_id=:id AND action='0' ");
$result->bindParam(":id", $_GET['id']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {

    $bulk_list .= '
                 <tr>
                    <td style="border-bottom: 1px solid #ccc;">' . $row['id'] . '</td>
                    <td style="border-bottom: 1px solid #ccc;">' . $row["cus"] . '</td>
                    <td style="border-bottom: 1px solid #ccc;">' . $row['invoice_no'] . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row['credit_amount'], 2) . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row["pay_amount"], 2) . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row['credit_amount'] - $row["pay_amount"], 2) . '</td>
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
     					 <b style="font-size:15px">VAT No: </b> ' . $info_vat . '<br>
                        <p>Date: ' . date('Y-M-d') . ' Time:' . date('H:m') . '</p><br><br><br>
                    </td>
                    <td align="right" valign="top" width="40%">
                        <b style="font-family:Poppins; font-size:30px">' . $in_type . '</b><br><br>
                        <b style="font-size:15px">Invoice: ' . $invo . '</b> <br>
                        <b style="font-size:15px">Invoice Date:  ' . $invo_date . '</b><br>
                        <b style="font-family: Poppins; font-size:14px"> Type: ' . ucfirst($type) . '</b><br>
                        <b style="font-family: Poppins; font-size:14px"> Amount: ' . $amount . '</b><br>
                        ' . $chq_details . '<br><br><br>
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
                    <th style="border-bottom: 1px solid #ccc;">Customer</th>
                    <th style="border-bottom: 1px solid #ccc;">Invoice</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Credit Amount</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Pay Amount</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Balance</th>
                </tr>

                ' . $bulk_list . '
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
            <table cellpadding="0" cellspacing="0" border="0">
                <tr align="left">
                    <td align="center"><img src="../icon/r.png" width="40" alt=""></td>
                </tr>
                <tr align="left">
                    <td align="center">CLOUD ARM</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
';

$contact = $_GET['number'];
// $contact = '94762020312';
if (!empty($contact)) {
    $text = 'This is a system generated document ...';
    $url = get_pdf($output, 'bulk', 'bin/');
    whatsApp($contact, $text, $url);
}

$return = $_SESSION['SESS_BACK'];

header("location: ../$return");
