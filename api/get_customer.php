<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
//$user=$_POST['user'];



$result = $db->prepare("SELECT * FROM customer ");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "id" => $row['customer_id'],
        "name" => $row['customer_name'],
        "address" =>$row['address'],
        'contact' =>$row['contact'],
        'area' =>$row['area'],
        'vat_no' =>$row['vat_no'],
        'root_id' =>$row['root_id'],
        'root'=>$row['root'],
        'price12'=>$row['price_12'],
        'price5'=>$row['price_5'],
        'price37'=>$row['price_37'],
        'price2'=>$row['price_2'],
    );
    }
 




echo (json_encode ( $result_array ));
