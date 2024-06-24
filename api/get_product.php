<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
//$user=$_POST['user'];

$load_id=$_POST['id'];

$result = $db->prepare("SELECT *, products.price as pprice FROM products JOIN loading_list ON products.product_id = loading_list.product_code WHERE loading_list.loading_id='$load_id' ");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "name" => $row['gen_name'],
        "loading_id" => $row['loading_id'],
        "product_id" => $row['product_id'],
        "price_id" => $row['price_id'],
        "price" => $row['pprice'],
        "price2" => $row['price2'],
        "sell" => $row['sell_price'],
        "cost" => $row['o_price'],
        "qty" => $row['qty'],
        "qty_sold" => $row['qty_sold'],
        "action"=>$row['action'],
        "img"=>$row['img'],
    );
    }
 




echo (json_encode ( $result_array ));
