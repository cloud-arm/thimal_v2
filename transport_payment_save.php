<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$now = date("Y-m-d");
$time = date('H:i:s');

$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$id = $_POST['id'];
$date = $_POST['date'];
$amount = $_POST['amount'];
$sup_invo = '';

$result = $db->prepare("SELECT * FROM transport WHERE transaction_id = :id  ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $total = $row['amount'];
    $pay_amount = $row['pay_amount'];
    $invo = $row['invoice_number'];
    $sup = $row['supplier_id'];
    $sup_name = $row['supplier_name'];
    $date_from = $row['date_from'];
    $date_to = $row['date_to'];
}

$action = 2;
if ($total > ($pay_amount + $amount)) {
    $action = 1;
}

$sql = "UPDATE transport SET  action=?, pay_amount = pay_amount + ?, pay_date = ? WHERE transaction_id=?";
$ql = $db->prepare($sql);
$ql->execute(array($action, $amount, $date, $id));

$pay_type = 'Credit_Note';
$type = 'Transport';
$note = 'This is a transport payment. This payment has been made from ' . $date_from . ' to ' . $date_to . '..';

// payment section
$sql = 'INSERT INTO supply_payment (amount,pay_amount,pay_type,date,invoice_date,invoice_no,supply_id,supply_name,supplier_invoice,type,credit_balance,reason) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
$q = $db->prepare($sql);
$q->execute(array($amount, 0, $pay_type, $now, $date, 'tr' . $invo, $sup, $sup_name, $sup_invo, $type, $amount, $note));


if ($action == 2) {
    $d = date('Y/m/d');
    header("location: transport_payment_rp.php?dates=$d-$d");
} else {
    header("location: transport_payment.php");
}
