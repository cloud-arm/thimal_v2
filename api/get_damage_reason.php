<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// respond init
$result_array = [];

// create success respond 
$result = $db->prepare("SELECT * FROM damage_reason ");
$result->bindParam(':id', $res);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $result_array[] = array(
        "id" => $row['id'],
        "name" => $row['name']
    );
}

// send respond
echo (json_encode($result_array));
