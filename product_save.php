<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('connect.php');
$user_id = $_SESSION['SESS_MEMBER_ID'];

$date = date('Y-m-d');
$time = date('H.i.s');

$id = $_POST['id'];
$name = $_POST['name'];
$cost = $_POST['cost'];
$sell = $_POST['sell'];

// query
if ($id == 0) {

    $sql = "INSERT INTO products (gen_name,price,o_price) VALUES (?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($name, $sell, $cost));
} else {

    $price = $_POST['price'];
    $price2 = $_POST['price2'];
    $commission = $_POST['commission'];
    $tr1 = $_POST['transport1'];
    $tr2 = $_POST['transport2'];

    $result = $db->prepare("SELECT * FROM products WHERE product_id=:id ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $old_d = $row['price'];
        $old_d2 = $row['price2'];
        $old_o = $row['o_price'];
        $old_sell = $row['sell_price'];
    }

    $sql = "INSERT INTO price_update (name,product_id,old_d_price,old_d_price2,old_sell_price,old_o_price,d_price,d_price2,sell_price,o_price,date,time,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($name, $id, $old_d, $old_d2, $old_sell, $old_o, $price, $price2, $sell, $cost, $date, $time, $user_id));

    $result = $db->prepare("SELECT id FROM price_update WHERE product_id=:id ORDER by id DESC limit 0,1 ");
    $result->bindParam(':id', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $price_id = $row['id'];
    }

    $sql = "UPDATE products
        SET gen_name=?, price=?, price2=?, o_price=?, sell_price=?, price_id=?, commission=?, transport1=?, transport2=?
		WHERE product_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($name, $price, $price2, $cost, $sell, $price_id, $commission, $tr1, $tr2, $id));
}


header("location: product.php");
