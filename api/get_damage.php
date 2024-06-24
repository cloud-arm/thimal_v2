<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
//$user=$_POST['user'];


$result_array=[];
$result = $db->prepare("SELECT * FROM damage ");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "id" => $row['id'],
        "complain_no" => $row['complain_no'],
        "customer_name" => $row['customer_name'],
        "customer_id" => $row['customer_id'],
        "product_id" => $row['product_id'],
        "product_name" => $row['cylinder_type'],
        "reason" => $row['reason'],
        "location" => $row['location'],
        "action" => $row['action'],
        "cylinder_no" => $row['cylinder_no'],
    );
    }
 




echo (json_encode ( $result_array ));
