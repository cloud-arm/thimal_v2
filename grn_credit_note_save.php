<?php
session_start();
include('connect.php');

$sup = $_POST['supply'];
$sup_invo = $_POST['invoice'];
$amount = $_POST['amount'];
$note = $_POST['note'];

$date = date("Y-m-d");
$time = date('H:i:s');
$invo = 'cn' . date("ymdhis");

$result = $db->prepare("SELECT * FROM supplier WHERE supplier_id=:id ");
$result->bindParam(':id', $sup);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sup_name = $row['supplier_name'];
}

$sql = 'INSERT INTO supply_payment (amount,pay_type,date,invoice_no,supply_id,supply_name,supplier_invoice,type,credit_balance,reason) VALUES (?,?,?,?,?,?,?,?,?,?)';
$q = $db->prepare($sql);
$q->execute(array($amount, 'Credit_note', $date, $invo, $sup, $sup_name, $sup_invo, 'Return', $amount, $note));



header("location: grn_credit_note.php");
