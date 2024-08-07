<?php
session_start();
include('../../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Retrieve the transaction ID from the POST data
$id = $_POST['id'] ?? null;

if ($id === null) {
    echo json_encode(array("message" => "Error: Missing parameters."));
    exit();
}


try {
    // Prepare and execute the SQL query
    $result = $db->prepare("SELECT transaction_id AS loading_id, sync, action  FROM loading WHERE driver = :id  AND action = 'load' ");
    $result->bindParam(':id', $id);
    $result->execute();

    // Fetch the results and create an array
    for($i=0; $row = $result->fetch(); $i++){
        $result_array = array (
            "action" => $row['action'],
            "sync" => $row['sync'],
            "loading_id" => $row['loading_id'],
        );
        }

    // Encode the array into JSON and output it
    echo json_encode($result_array);
} catch (PDOException $e) {
    echo json_encode(array("message" => "Error: " . $e->getMessage()));
}
