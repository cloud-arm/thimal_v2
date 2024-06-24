<?php
session_start();
include('config.php');
date_default_timezone_set("Asia/Colombo");


$date = date("Y-m-d");
$time = date('H:i:s');

$invoice = $_POST['id'];
$sales_id = $_POST['sales_id'];
$pid = $_POST['product'];
$qty = $_POST['qty'];
$price = $_POST['price'];
$dic = $_POST['dic'];

$sid = base64_encode($sales_id);
$invo = base64_encode($invoice);

//get old sales
$result = select("sales", "loading_id,date", "transaction_id = '" . $sales_id . "'");
for ($i = 0; $row = $result->fetch(); $i++) {
    $load = $row['loading_id'];
    $date = $row['date'];
}

//get product
$result = select("products", "commission,gen_name", "product_id = '" . $pid . "'");
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['gen_name'];
    $comm = $row['commission'];
}


//checking stock qty
$con_qty = 0;
$result = select("products", "qty", "product_id = '" . $pid . "'");
for ($i = 0; $row = $result->fetch(); $i++) {
    $con_qty = $row['qty'];
}


if ($con_qty >= $qty) {

    //checking duplicate
    $con = 0;
    $result = select("sales_list", "id", "invoice_no = '" . $invoice . "' AND product_id = '" . $pid . "'");
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
        $cus = 0;
        $price_id = 0;

        $insertData = array(
            "data" => array(
                "invoice_no" => $invoice,
                "product_id" => $pid,
                "name" => $name,
                "amount" => $amount,
                "cost_amount" => $cost_amount,
                "dic" => $discount,
                "qty" => $qty,
                "price" => $price,
                "profit" => $profit,
                "date" => $date,
                "loading_id" => $load,
                "action" => 1,
                "cus_id" => $cus,
                "price_id" => $price_id,
                "vat" => $vat,
                "value" => $value,
                "commission" => $commission,
            ),
            "other" => array(
                "data_id" => $invoice,
                "data_name" => "invoice",
            ),
        );

        $status = insert("sales_list", $insertData);

        if ($status['status'] == 'success') {
            header("location: sales_loading_sales.php?sid=$sid&id=$invo");
        } else {
            $err = 3;
            $msg = base64_encode($status['message']);

            header("location: sales_loading_sales.php?sid=$sid&id=$invo&err=$err&msg=" . $msg);
        }
    } else {
        $err = 2;

        header("location: sales_loading_sales.php?sid=$sid&id=$invo&err=$err");
    }
} else {
    $err = 1;

    header("location: sales_loading_sales.php?sid=$sid&id=$invo&err=$err");
}
