<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$result_array = array();

$result = $db->prepare("SELECT * FROM products ");
$result->bindParam(':userid', $res);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $result_array[] = array(
        "id" => $row['product_id'],
        "name" => $row['gen_name'],
        "price" => $row['price'],
        "qty" => $row['qty']
    );
}





echo (json_encode($result_array));
