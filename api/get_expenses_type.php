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
$result = $db->prepare("SELECT * FROM expenses_sub_type WHERE type_id = '2'");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "id" => $row['id'],
        "type_id" => $row['type_id'],
        "name" => $row['name'],
    );
    }
 




echo (json_encode ( $result_array ));
