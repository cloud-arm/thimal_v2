<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$user = $_SESSION['SESS_MEMBER_ID'];

$sid = $_POST['stock'];
$qty = $_POST['qty'];
$cost = $_POST['cost'];
$type = $_POST['type'];
$invo = $_POST['invo'];

$load = 0;
$dic = 0;
$sell = 0;
$pid = 0;


if ($dic == '') {
    $dic = 0;
}


$result = $db->prepare("SELECT * FROM stock WHERE id=:id ");
$result->bindParam(':id', $sid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $pid = $row['product_id'];
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
    $re->execute(array($invo, $product_name, $qty, $amount, $date, $pid, $cost, $sell, $dic, $type, $user, $sid, $load));


    header("location: grn_return.php?id=$invo");
} else {

    header("location: grn_return.php?id=$invo&err=1");
}
