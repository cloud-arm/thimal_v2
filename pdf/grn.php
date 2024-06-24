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


$in_type = "PURCHASES";

if (isset($_GET['invo'])) {
    $invoice_no = $_GET['invo'];
    $sql = "SELECT * FROM purchases WHERE  invoice_number='$invoice_no'  ";
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM purchases WHERE   transaction_id='$id'  ";
}

$result1 = $db->prepare($sql);
$result1->bindParam(':userid', $res);
$result1->execute();
for ($i = 0; $row1 = $result1->fetch(); $i++) {

    $purchases_list = "";
    $result = $db->prepare("SELECT * FROM purchases_item WHERE  invoice=:id ");
    $result->bindParam(":id", $row1['invoice_number']);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {

        $purchases_list .= '
                 <tr>
                    <td style="border-bottom: 1px solid #ccc;">' . ($i + 1) . '</td>
                    <td style="border-bottom: 1px solid #ccc;">' . $row["name"] . '</td>
                    <td align="center" style="border-bottom: 1px solid #ccc;">' . $row["qty"] . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row['cost'], 2) . '</td>
                    <td align="right" style="border-bottom: 1px solid #ccc;">' . number_format($row["amount"], 2) . '</td>
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
                        <img src="../icon/Logo-Laugfs-Gas.png" alt="Logo" style="max-width:150px;"><br>
                         <b style="font-family: Poppins; font-size:17px">' . $info_name . '</b><br>
                         <b style="font-family: Poppins; font-size:15px">' . $info_add . '</b><br>
                         <b style="font-family: Poppins; font-size:15px">' . $info_con . '</b><br>
     					 <b style="font-size:15px">VAT No: </b> ' . $info_vat . '<br>
                         <p>Date: ' . date('Y-M-d') . ' Time:' . date('H:m') . '</p><br><br><br>
                    </td>
                    <td align="right" valign="top" width="40%">
                        <b style="font-family: Poppins; font-size:14px"> Laugfs Gas PLC </b><br>
                        <b style="font-family: Poppins; font-size:14px"> 101, Maya Avenue, </b><br>
                        <b style="font-family: Poppins; font-size:14px"> Colombo 06, Sri Lanka </b><br>
                        <b style="font-family: Poppins; font-size:14px"> Tel : +94 11 5 566 222 </b><br>
                        <b style="font-family: Poppins; font-size:14px"> Fax : +94 11 5 577 824 </b><br><br>
                        <b style="font-family:Poppins; font-size:30px">' . $in_type . '</b><br>
                        <b style="font-size:15px">Invoice: ' . $invo . '</b> <br>
                        <b style="font-size:15px">Purchase Date:  ' . $row1['pu_date'] . '</b><br>
                        <b style="font-size:15px">Location:  ' . $row1['location'] . '</b><br><br>
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
                    <th style="border-bottom: 1px solid #ccc;">Name</th>
                    <th style="border-bottom: 1px solid #ccc;">Quantity</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Cost Price</th>
                    <th align="right" style="border-bottom: 1px solid #ccc;">Total</th>
                </tr>

                ' . $purchases_list . '

                <tr>
                    <td style="font-size:20px" colspan="4" align="right"><h3>Total:</h3></td>
                    <td style="font-size:20px" align="right"><h3>Rs.' . number_format($row1["amount"], 2) . '</h3></td>
                </tr>
                <tr>
                    <td align="center"><img src="../icon/r.png" width="40" alt=""></td>
                    <td colspan="3" align="right">Pay Amount:</td>
                    <td align="right">Rs.' . number_format($row1["pay_amount"], 2) . '</td>
                </tr>
                <tr>
                    <td align="center">CLOUD ARM</td>
                    <td colspan="3" align="right">Balance:</td>
                    <td align="right">Rs.' . number_format($row1["amount"] - $row1["pay_amount"], 2) . '</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
';
}

$contact = $_GET['number'];
// $contact = '94762020312';
if (!empty($contact)) {
    $text = 'This is a system generated document ...';
    $url = get_pdf($output, 'grn', 'bin/');
    whatsApp($contact, $text, $url);
}
$return = $_SESSION['SESS_BACK'];

header("location: ../$return");
