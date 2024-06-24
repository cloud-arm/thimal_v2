<?php
session_start();
include('connect.php');

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$invo = 'blk' . date('ymdhis');

$sup_id = $_POST['sup_id'];
$pay_type = $_POST['pay_type'];
$pay_amount = $_POST['amount'];
$note = $_POST['note'];

$chq_no = $_POST['chq_no'];
$chq_date = $_POST['chq_date'];

$date = date("Y-m-d");
$time = date('H:i:s');


$result = $db->prepare("SELECT * FROM supplier WHERE supplier_id=:id  ");
$result->bindParam(':id', $sup_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sup_name = $row['supplier_name'];
}


if ($pay_amount > 0) {

    $bank_id = 1;

    $re = $db->prepare("SELECT * FROM bank_balance WHERE id = '$bank_id' ");
    $re->bindParam(':id', $bank_id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $chq_bank = $r['name'];
    }

    $sql = 'INSERT INTO supply_payment(amount,pay_amount,pay_type,date,invoice_no,supply_id,supply_name,type,bank_id,chq_no,chq_bank,chq_date,reason,action) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $q = $db->prepare($sql);
    $q->execute(array($pay_amount, 0, $pay_type, $date, $invo, $sup_id, $sup_name, 'bulk_payment', $bank_id, $chq_no, $chq_bank, $chq_date, $note, 11));
}


header("location: grn_bulk_payment.php?id=$invo");
