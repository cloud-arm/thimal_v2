<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
$id=$_POST['id'];

$type=$_POST['type'];

if($type=='sync'){
    $sql = 'UPDATE  loading SET sync =? WHERE transaction_id =? ';
    $ql = $db->prepare($sql);
    $ql->execute(array($type,$id));
}

$result = $db->prepare("SELECT * FROM loading WHERE transaction_id='$id' ");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "lorry_no" => $row['lorry_no'],
        "lorry_id" => $row['lorry_id'],
        "root_id" => $row['root_id'],
        "driver_id" => $row['driver'],
        "helper1_id" => $row['helper1'],
        "helper2_id" => $row['helper2'],
        "loading_id" => $row['transaction_id'],
    );
    }
 




echo (json_encode ( $result_array ));
