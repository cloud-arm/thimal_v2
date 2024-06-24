<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
//$user=$_POST['user'];

$load_id = 1;

$result = $db->prepare("SELECT * FROM payment JOIN customer ON payment.customer_id=customer.customer_id WHERE payment.type='credit' AND payment.pay_amount < payment.amount AND payment.action > 0 AND payment.dll = 0 ");
$result->bindParam(':userid', $res);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $result_array[] = array(
        "name" => $row['customer_name'],
        "cus_id" => $row['customer_id'],
        "amount" => $row['amount'],
        "balance" => $row['amount'] - $row['pay_amount'],
        "type" => $row['type'],
        "invoice_no" => $row['invoice_no'],
        "date" => $row['date'],
        "id" => $row['transaction_id'],
    );
}





echo (json_encode($result_array));
