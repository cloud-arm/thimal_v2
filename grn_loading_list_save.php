<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$user_id = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$id = $_POST['id'];

$date = date("Y-m-d");
$time = date("h:i.a");
$invo = 'pu' . date('ymdhis');


// Purchases section
$result = $db->prepare("SELECT * FROM loading_list WHERE  loading_id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $pid = $row['product_code'];
    $qty = $row['qty'];

    $gas = 0;
    $res = $db->prepare("SELECT * FROM products WHERE product_id= :id");
    $res->bindParam(':id', $pid);
    $res->execute();
    for ($k = 0; $ro = $res->fetch(); $k++) {
        $gas = $ro['product_name'];
    }

    $res = $db->prepare("SELECT * FROM products WHERE product_id= :id");
    $res->bindParam(':id', $gas);
    $res->execute();
    for ($f = 0; $ro = $res->fetch(); $f++) {
        $gas_name = $ro['gen_name'];
        $price = $ro['cost'];
        $sell = $ro['sell_price'];
    }


    $amount = $price * $qty;
    $dic = 0;
    $stock = 0;
    $type = 'GRN';

    $sql = "INSERT INTO purchases_item (invoice,name,qty,amount,date,product_id,cost,sell,discount,type,user_id,stock_id,loading_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $re = $db->prepare($sql);
    $re->execute(array($invo, $gas_name, $qty, $amount, $date, $gas, $price, $sell, $dic, $type, $user_id, $stock, $id));
}

header("location: grn.php?id=$id&invo=$invo");