<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('connect.php');

$unit = $_POST['unit'];

if ($unit == 1) {

    $cus_id = $_POST['cus'];
    $price = $_POST['price'];
    $pro_id = $_POST['pro_id'];

    $result = $db->prepare("SELECT * FROM customer WHERE  customer_id= :id ");
    $result->bindParam(':id', $cus_id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $cus_id = $row['customer_id'];
        $cus_name = $row['customer_name'];
    }

    $result = $db->prepare("SELECT * FROM products WHERE  product_id=:id   ");
    $result->bindParam(':id', $pro_id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $pro_name = $row['gen_name'];
        $n_price = $row['price'];
    }


    // query
    $sql = "INSERT INTO special_price (product_name,product_id,price,n_price,customer,customer_id) VALUES (?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($pro_name, $pro_id, $price, $n_price, $cus_name, $cus_id));
}

if ($unit == 2) {

    $price = $_POST['price'];
    $up_price = $_POST['up_price'];
    $product = $_POST['product'];

    $sql = "UPDATE special_price SET price=? WHERE price=? AND product_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($up_price, $price, $product));
}
header("location: special_price.php");
