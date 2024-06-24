<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$date = date("Y-m-d");
$time = date('H:i:s');

$invoice = $_POST['id'];
$pid = $_POST['product'];
$qty = $_POST['qty'];
$price = $_POST['price'];
$dic = $_POST['dic'];


//get product
$result = $db->prepare("SELECT * FROM products WHERE product_id = :id  ");
$result->bindParam(':id', $pid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['gen_name'];
    $comm = $row['commission'];
}


//checking stock qty
$con_qty = 0;
$result = $db->prepare("SELECT qty FROM products WHERE product_id = :id  ");
$result->bindParam(':id', $pid);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $con_qty = $row['qty'];
}


if ($con_qty >= $qty) {

    //checking duplicate
    $con = 0;
    $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = '$invoice' AND product_id = '$pid'  ");
    $result->bindParam(':id', $cus);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $con = $row['id'];
    }

    if ($con == 0) {

        // amount
        $amount = $price * $qty;

        // vat value
        $vat = ($amount / 118) * 18;

        // without vat amount
        $value = ($amount / 118) * 100;

        $discount = 0;

        if ($dic > 0 & $dic < 100) {
            //discount
            $discount = $amount * $dic / 100;

            $amount = $amount - $discount;
        }

        // commission
        $commission = $comm * $qty;

        $cost_amount = 0;
        $profit = 0;
        $load = 0;
        $cus = 0;
        $price_id = 0;

        // insert query
        $sql = "INSERT INTO sales_list (invoice_no,product_id,name,amount,cost_amount,dic,qty,price,profit,date,loading_id,action,cus_id,price_id,vat,value,commission) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($invoice, $pid, $name, $amount, $cost_amount, $discount, $qty, $price, $profit, $date, $load, 1, $cus, $price_id, $vat, $value, $commission));


        header("location: sales.php?id=$invoice");
    } else {
        $err = 2;

        header("location: sales.php?id=$invoice&err=$err");
    }
} else {
    $err = 1;

    header("location: sales.php?id=$invoice&err=$err");
}
