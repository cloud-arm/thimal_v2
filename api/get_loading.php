<?php
session_start();
include('../connect.php');
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
    $result = $db->prepare("SELECT transaction_id AS loading_id, helper2 AS helper2_id, helper1 AS helper1_id, rep AS driver_name, driver AS driver_id, root_id, lorry_id, lorry_no  FROM loading WHERE transaction_id = :id ");
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
