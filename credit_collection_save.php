<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$date = date("Y-m-d");
$time = date('H:i:s');

$id = $_GET['id'];

$error_id = 0;

$result = $db->prepare("SELECT * FROM collection WHERE  id=:id  ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
  $invoice_no = $row['invoice_no'];
  $customer_id = $row['customer_id'];
  $loading_id = $row['loading_id'];
  $amount = $row['amount'];
  $chq_no = $row['chq_no'];
  $chq_date = $row['chq_date'];
  $bank = $row['bank'];
  $type = $row['pay_type'];
  $pay_id = $row['pay_id'];
}


if ($pay_id == 0) {

  $action = 11;
  $pay_amount = 0;

  if ($type == "chq") {
    if ($chq_no == "" || $chq_date == "" || $bank == "") {
      $error_id = 5;
    }
  }

  $credit = 0;
  if ($amount > $pay_amount) {
    $credit = $amount - $pay_amount;
  }

  $sales_id = 0;


  $sql = "INSERT INTO payment (collection_id,pay_amount,amount,type,pay_type,date,chq_no,chq_date,chq_bank,sales_id,customer_id,pay_credit,action,loading_id,credit_balance,time,invoice_no,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($id, $pay_amount, $amount, $type, $type, $date, $chq_no, $chq_date, $bank, $sales_id, $customer_id, 1, $action, $loading_id, $credit, $time, $invoice_no, 'credit_payment'));


  $result = $db->prepare("SELECT * FROM payment WHERE collection_id=:id ");
  $result->bindParam(':id', $id);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $pay_id = $row['transaction_id'];
  }

  $sql = "UPDATE collection SET pay_id=? WHERE id=?";
  $q = $db->prepare($sql);
  $q->execute(array($pay_id, $id));

  $sql = "UPDATE credit_payment SET pay_id=? WHERE collection_id=?";
  $q = $db->prepare($sql);
  $q->execute(array($pay_id, $id));
}



header("location: bulk_payment.php?id=$pay_id");
