<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$error_id = "m";
$date = date("Y-m-d");


$invo = $_POST['invo'];
$amount = $_POST['amount'];
$pay_id = $_POST['id'];

if ($invo == "qb") {
  $sales_id = 0;
  $c_amount = 0;
  $cus_id = 0;
  $type = "qb";
  $invo = 0;
  $coll_id = 0;
  $cus = "Old bill";
} else {


  $result = $db->prepare("SELECT * FROM credit_payment WHERE tr_id='$invo' AND action='2' AND  pay_id='$pay_id' ");
  $result->bindParam(':id', $invo);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $error_id = $row['id'];
  }


  $result = $db->prepare("SELECT * FROM payment WHERE transaction_id=:id  ");
  $result->bindParam(':id', $invo);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $sales_id = $row['sales_id'];
    $c_amount = $row['amount'];
    $cus_id = $row['customer_id'];
    $invoice_no = $row['invoice_no'];
  }


  $result = $db->prepare("SELECT * FROM payment WHERE transaction_id=:id  ");
  $result->bindParam(':id', $pay_id);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $pay_invoice = $row['invoice_no'];
    $coll_id = $row['collection_id'];
    $type = $row['type'];
  }


  $result = $db->prepare("SELECT * FROM customer WHERE customer_id=:id  ");
  $result->bindParam(':id', $cus_id);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $cus = $row['customer_name'];
  }

  if ($c_amount < $amount) {
    $error_id = 1;
  }
}

if ($error_id == "m") {
  $act = 2;


  $sql = "INSERT INTO credit_payment (tr_id,sales_id,pay_id,pay_amount,credit_amount,cus_id,date,action,cus,type,collection_id,invoice_no,pay_invoice) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($invo, $sales_id, $pay_id, $amount, $c_amount, $cus_id, $date, $act, $cus, $type, $coll_id, $invoice_no, $pay_invoice));


  header("location: bulk_payment.php?id=$pay_id");
} else {

  header("location: bulk_payment.php?id=$pay_id&unit=1&error=$error_id");
}
