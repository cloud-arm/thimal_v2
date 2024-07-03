<?php
session_start();
include('connect.php');
include("config.php");
include('log/log.php');
date_default_timezone_set("Asia/Colombo");

$a = 0;
$result = $db->prepare("SELECT transaction_id,invoice_no FROM payment WHERE type = 'credit' AND paycose = 'invoice_payment' AND  action = 2 ");
$result->bindParam(':id', $row);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
  $id = $row['transaction_id'];
  $invoice = $row['invoice_no'];

  $con = 0;
  $result1 = $db->prepare("SELECT transaction_id FROM sales WHERE action = 0 AND invoice_number = :id ");
  $result1->bindParam(':id', $invoice);
  $result1->execute();
  for ($i = 0; $row1 = $result1->fetch(); $i++) {
    $con = $row1['transaction_id'];
  }

  if ($con) {
    $sql = "UPDATE payment SET credit_balance = ?, action = ?, paycose = ?  WHERE transaction_id = ? ";
    $ql = $db->prepare($sql);
    $ql->execute(array(0, 8, 'invoice_delete', $id));
    $a++;
  }
}

echo $a;