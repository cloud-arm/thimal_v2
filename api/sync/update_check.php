<?php
session_start();
include('../../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$id=$_POST['id'];
// Prepare and execute the SQL query
$result = $db->prepare("SELECT * FROM update_record WHERE id > '$id' ORDER BY id DESC LIMIT 1");
$result->execute();

// Fetch data as an associative array
$data = $result->fetchAll(PDO::FETCH_ASSOC);

// Output the JSON encoded data
echo json_encode($data);

