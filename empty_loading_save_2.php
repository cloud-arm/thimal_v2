<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");



$pid = $_POST['product'];
$id = $_POST['id'];
$qty = $_POST['qty'];


$date = date("Y-m-d");
$time = date("h:i.a");

$action = "load";
$user_id = $_SESSION['SESS_MEMBER_ID'];


$result = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id  ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $lorry = $row['lorry_no'];
}


$gas = 0;
$result = $db->prepare("SELECT * FROM products WHERE product_id= :id");
$result->bindParam(':id', $pid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $product_name = $row['gen_name'];
    $gas = $row['product_name'];
}

$result = $db->prepare("SELECT * FROM products WHERE product_id= :id");
$result->bindParam(':id', $gas);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $price = $row['cost'];
}


$qty_blc = 0;
$result = $db->prepare("SELECT * FROM products WHERE product_id= :id");
$result->bindParam(':id', $pid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $qty_blc = $row['qty'];
}

if ($qty_blc >= $qty) {

    // Empty loading section
    $sql = "UPDATE products SET qty=qty-? WHERE product_id=? ";
    $q = $db->prepare($sql);
    $q->execute(array($qty, $pid));


    $result = $db->prepare("SELECT * FROM products WHERE product_id= :id");
    $result->bindParam(':id', $pid);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $qty_blc = $row['qty'];
    }


    $result = $db->prepare("SELECT * FROM loading_list WHERE product_code='$pid' and loading_id='$id'");
    $result->bindParam(':id', $b);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $loading_list_id = $row['transaction_id'];
    }

    // query
    if (isset($loading_list_id)) {

        $sql = "UPDATE loading_list SET qty = qty + ?, qty_sold = qty_sold + ? WHERE transaction_id=?";
        $q = $db->prepare($sql);
        $q->execute(array($qty, $qty, $loading_list_id));
    } else {

        $sql = "INSERT INTO loading_list (product_code,qty,qty_sold,price,lorry_no,product_name,date,action,loading_time,loading_id,load_yard_before) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $q = $db->prepare($sql);
        $q->execute(array($pid, $qty, $qty, $price, $lorry, $product_name, $date, $action, $time, $id, $qty_blc));
    }


    $sql = "INSERT INTO stock_log (product_id,qty,product_name,date,time,action,source_id,yard_qty,type,user_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($pid, $qty, $product_name, $date, $time, 1, $id, $qty_blc, 1, $user_id));


    header("location: empty_loading_2.php?id=$id");
} else {
    $err = 1;

    header("location: empty_loading_2.php?id=$id&err=$err");
}
