<?php
session_start();
include('../../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
$id=$_POST['id'];

$load_id=1;

$result = $db->prepare("SELECT * FROM loading WHERE driver='$id' AND action='load' ");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array = array (
        "action" => $row['action'],
        "sync" => $row['sync'],
        "loading_id" => $row['transaction_id'],
    );
    }
 




echo (json_encode ( $result_array ));
