<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$result = $db->prepare("DELETE FROM sales_list WHERE  id= :id");
$result->bindParam(':id', $_GET['id']);
$result->execute();


$result = $db->prepare("SELECT sum(amount) FROM sales_list WHERE invoice_no= :id ");
$result->bindParam(':id', $_GET['invo']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $amount = $row['sum(amount)'];
}

echo number_format($amount, 2);
