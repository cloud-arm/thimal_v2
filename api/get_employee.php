<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//echo $r_data[0];
$id=$_POST['id'];


$result_array=[];
$result = $db->prepare("SELECT * FROM loading JOIN employee ON loading.driver=employee.id WHERE loading.transaction_id='$id' AND loading.driver > 0 ");
$result->bindParam(':userid', $res);
$result->execute();
for($i=0; $row = $result->fetch(); $i++){
    $result_array[] = array (
        "id" => $row['transaction_id'],
        "action" => $row['action'],
        "type" => "Driver",
        "emp_id" => $row['driver'],
        "name" => $row['name'],
    );
    }

    $result = $db->prepare("SELECT * FROM loading JOIN employee ON loading.helper1=employee.id WHERE loading.transaction_id='$id' AND loading.helper1 > 0 ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for($i=0; $row = $result->fetch(); $i++){
        $result_array[] = array (
            "id" => $row['transaction_id'],
            "action" => $row['action'],
            "type" => "Helper1",
            "emp_id" => $row['helper1'],
            "name" => $row['name'],
        );
        }

    $result = $db->prepare("SELECT * FROM loading JOIN employee ON loading.helper2=employee.id WHERE loading.transaction_id='$id' AND loading.helper2 > 0 ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for($i=0; $row = $result->fetch(); $i++){
        $result_array[] = array (
            "id" => $row['transaction_id'],
            "action" => $row['action'],
            "type" => "Helper2",
            "emp_id" => $row['helper2'],
            "name" => $row['name'],
        );
        }

    $result = $db->prepare("SELECT * FROM loading JOIN employee ON loading.helper3=employee.id WHERE loading.transaction_id='$id' AND loading.helper3 > 0 ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for($i=0; $row = $result->fetch(); $i++){
        $result_array[] = array (
            "id" => $row['transaction_id'],
            "action" => $row['action'],
            "type" => "Helper3",
            "emp_id" => $row['helper3'],
            "name" => $row['name'],
        );
        }


 




echo (json_encode ( $result_array ));
