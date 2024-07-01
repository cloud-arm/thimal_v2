<?php
session_start();
include('../../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$id = $_POST['id'];


try {
    // Prepare and execute the SQL query
    $result = $db->prepare("SELECT transaction_id AS loading_id, sync, action  FROM loading WHERE driver = :id  AND action = 'load' ");
    $result->bindParam(':id', $id);
    $result->execute();

    // Fetch the results and create an array
    $result_array = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $result_array[] = $row;
    }

    // Encode the array into JSON and output it
    echo json_encode($result_array);
} catch (PDOException $e) {
    echo json_encode(array("message" => "Error: " . $e->getMessage()));
}
