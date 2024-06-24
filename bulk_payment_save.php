<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$time = date('H:i:s');

$invo = 'blk' . date('ymdhis');


$type = $_POST['type'];
$amount = $_POST['amount'];
$customer = $_POST['customer'];

$chq_no = '';
$chq_date = '';
$bank = 0;
$credit_note = 0;

if ($type == "chq") {
  $chq_no = $_POST['chq_no'];
  $chq_date = $_POST['chq_date'];
}

if ($type == "bank") {
  $bank = $_POST['bank'];
}

if ($type == "credit_note") {
  $credit_note =  $_POST['credit_note'];

  $result = $db->prepare("SELECT amount,customer_id FROM payment WHERE transaction_id = :id ");
  $result->bindParam(':id', $credit_note);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $amount = $row['amount'];
    $customer = $row['customer_id'];
  }
}

$con = 0;
$result = $db->prepare("SELECT * FROM payment WHERE type='chq' AND chq_no='$chq_no' AND amount='$amount' AND action = 2  ");
$result->bindParam(':id', $date);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
  $con = $row['transaction_id'];
}

if ($con == 0) {

  $action = 11;
  $pay_amount = 0;
  $bank_name = '';
  $chq_bank = '';

  if ($type == "bank") {

    $result = $db->prepare("SELECT * FROM bank_balance WHERE id=:id  ");
    $result->bindParam(':id', $bank);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
      $bank_name = $row['name'];
    }
  }

  $credit = 0;


  $sql = "INSERT INTO payment (pay_amount,amount,type,pay_type,date,chq_no,chq_date,chq_bank,bank_name,bank_id,pay_credit,action,credit_balance,time,invoice_no,paycose,credit_note_id,customer_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($pay_amount, $amount, $type, $type, $date, $chq_no, $chq_date, $chq_bank, $bank_name, $bank, 1, $action, $credit, $time, $invo, 'credit_payment', $credit_note, $customer));


  $result = $db->prepare("SELECT * FROM payment WHERE invoice_no=:id ");
  $result->bindParam(':id', $invo);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $pay_id = $row['transaction_id'];
  }

  header("location: bulk_payment.php?id=$pay_id");
} else {

  header("location: bulk_payment.php?unit=3&error");
}
