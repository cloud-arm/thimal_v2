<?php
session_start();
include('connect.php');


$grn_invo = $_POST['invoice'];
$amount = $_POST['amount'];
$invo = $_POST['id'];

$date = date("Y-m-d");
$time = date('H:i:s');

$error = 'none';


$result = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=:id ");
$result->bindParam(':id', $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $bank_id = $row['bank_id'];
    $chq_bank = $row['chq_bank'];
    $sup_id = $row['supply_id'];
    $sup_name = $row['supply_name'];
}

$result = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=:id AND pay_type='Credit'  AND credit_balance>0 ");
$result->bindParam(':id', $grn_invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $blc = $row['credit_balance'];
    $sup_invo = $row['supplier_invoice'];
}

$result = $db->prepare("SELECT * FROM bulk_payment WHERE invoice_no='$invo' AND grn_invoice_no='$grn_invo'  ");
$result->bindParam(':id', $grn_invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $error = $row['id'];
}

if ($blc < $amount) {
    $error = 'invalid';
}


if ($error == "none") {

    $sql = 'INSERT INTO bulk_payment(amount,date,invoice_no,grn_invoice_no,supply_id,supply_name,supplier_invoice,type,bank_id,chq_no,chq_bank,chq_date,forward_balance) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $q = $db->prepare($sql);
    $q->execute(array($amount, $date, $invo, $grn_invo, $sup_id, $sup_name, $sup_invo, '', $bank_id, $chq_no, $chq_bank, $chq_date, $blc));


    header("location: grn_bulk_payment.php?id=$invo");
} else {

    header("location: grn_bulk_payment.php?id=$invo&unit=1&error=$error");
}
