<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('connect.php');

$date = date("Y-m-d");
$time = date('H:i:s');

$product = $_POST['product'];
$month = $_POST['month'];
$target = $_POST['target'];

$d1 = $month . '-01';
$d2 = $month . '-13';

$result = $db->prepare("SELECT * FROM products WHERE  product_id=:id   ");
$result->bindParam(':id', $product);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['gen_name'];
}

$achieve = 0;
$balance = 0;
$bonus = 0;

$result = $db->prepare("SELECT sum(qty) FROM sales_list WHERE product_id = :id AND date BETWEEN '$d1' AND '$d2' ");
$result->bindParam(':id', $product);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $achieve = $row['sum(qty)'];
}

if ($target > $achieve) {
    $balance = $target - $achieve;
} else {
    $bonus = $achieve - $target;
}

// query
$sql = "INSERT INTO target (product_name,product_id,month,target,date,time,achievement,balance,bonus) VALUES (?,?,?,?,?,?,?,?,?)";
$q = $db->prepare($sql);
$q->execute(array($name, $product, $month, $target, $date, $time, $achieve, $balance, $bonus));

header("location: target.php");
