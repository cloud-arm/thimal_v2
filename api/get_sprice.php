<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
//$user=$_POST['user'];

$load_id=1;

$result = $db->prepare("SELECT * FROM special_price");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "product_name" => $row['product_name'],
        "product_id" => $row['product_id'],
        "customer_name"=>$row['customer'],
        "customer_id" => $row['customer_id'],
        "price"=>$row['price'],
        "n_price"=>$row['n_price'],
        "id"=>$row['id'],
    );
    }
 




echo (json_encode ( $result_array ));
