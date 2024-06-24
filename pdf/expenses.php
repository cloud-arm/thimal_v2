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

$result = $db->prepare("SELECT * FROM expenses_records WHERE  id=:id  ");
$result->bindParam(':id', $_GET['id']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $invo = $row['invoice_no'];
    $invo_date = $row['date'];
    $type = $row['type'];
    $type_id = $row['type_id'];
    $sub_type = $row['sub_type'];
    $amount = $row['amount'];
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $vendor = $row['vendor_id'];
}

$chq_details = '';
if ($type == 'chq' | $type == 'Chq') {
    $chq_details .= '<b style="font-family: Poppins; font-size:14px"> CHQ No: ' . $chq_no . '</b><br>';
    $chq_details .= '<b style="font-family: Poppins; font-size:14px"> CHQ Date: ' . $chq_date . '</b><br>';
}

$in_type = "EXPENSES";

$id = $_GET['id'];

$expenses_head = '';

$expenses_head .= '<th style="border-bottom: 1px solid #ccc;">ID</th>';
if ($sub_type > 0) {
    $expenses_head .= '<th style="border-bottom: 1px solid #ccc;">Sub Type</th>';
}
if ($type_id == 2) {
    $expenses_head .= '<th style="border-bottom: 1px solid #ccc;">Loading ID</th>';
}
if ($type_id == 2 | $type_id == 3) {
    $expenses_head .= '<th style="border-bottom: 1px solid #ccc;">Lorry NO</th>';
}
$expenses_head .= '<th style="border-bottom: 1px solid #ccc;">Type</th>';
if ($vendor > 0) {
    $expenses_head .= '<th style="border-bottom: 1px solid #ccc;">Vendor</th>';
}
$expenses_head .= '<th align="right" style="border-bottom: 1px solid #ccc;">Amount (Rs.)</th>';



$expenses_list = "";
$result = $db->prepare("SELECT * FROM expenses_records WHERE  invoice_no=:id AND paycose='payment' ");
$result->bindParam(":id", $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {

    $expenses_list .= '<tr><td style="border-bottom: 1px solid #ccc;">' . ($i + 1) . ' </td>';
    if ($sub_type > 0) {
        $expenses_list .= '<td style="border-bottom: 1px solid #ccc;">' . $row['sub_type_name'] . ' </td>';
    }
    if ($type_id == 2) {
        $expenses_list .= '<td style="border-bottom: 1px solid #ccc;">' . $row['loading_id'] . ' </td>';
    }
    if ($type_id == 2 | $type_id == 3) {
        $expenses_list .= '<td style="border-bottom: 1px solid #ccc;">' . $row['lorry_no'] . ' </td>';
    }
    $expenses_list .= '<td style="border-bottom: 1px solid #ccc;">' . $row['pay_type'] . ' </td>';
    if ($vendor > 0) {
        $expenses_list .= '<td style="border-bottom: 1px solid #ccc;">' . $row['vendor_name'] . ' </td>';
    }
    $expenses_list .= '<td align="right" style="border-bottom: 1px solid #ccc;">Rs.' . $row['amount'] . '</td></tr>';
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
                <tr> ' . $expenses_head . ' </tr>

                ' . $expenses_list . '
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
    $url = get_pdf($output, 'expenses', 'bin/');
    whatsApp($contact, $text, $url);
}

$return = $_SESSION['SESS_BACK'];

header("location: ../$return");
