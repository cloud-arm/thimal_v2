<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$u = $_SESSION['SESS_MEMBER_ID'];

$load = $_POST['id'];
$type = $_POST['type'];
$qty = $_POST['qty'];
$cost = $_POST['cost'];
$pid = $_POST['product'];
$dic = $_POST['dic'];
$invo = $_POST['invo'];

$dic = 0;
$sell = 0;
$stock = 0;


if ($dic == '') {
    $dic = 0;
}


$result = $db->prepare("SELECT * FROM products WHERE product_id=:id ");
$result->bindParam(':id', $pid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $product_name = $row['gen_name'];
    $sell = $row['sell_price'];
}


$amount = $cost * $qty;

$dic = $amount * $dic / 100;

$date = date("Y-m-d");

$con = 0;
$result = $db->prepare("SELECT * FROM purchases_item WHERE product_id=:id  AND invoice = '$invo' ");
$result->bindParam(':id', $pid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $con = $row['transaction_id'];
}

if ($con == 0) {
    $sql = "INSERT INTO purchases_item (invoice,name,qty,amount,date,product_id,cost,sell,discount,type,user_id,stock_id,loading_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $re = $db->prepare($sql);
    $re->execute(array($invo, $product_name, $qty, $amount, $date, $pid, $cost, $sell, $dic, $type, $u, $stock, $load));


    header("location: grn.php?id=$load&invo=$invo");
} else {

    header("location: grn.php?id=$load&invo=$invo&err=1");
}
