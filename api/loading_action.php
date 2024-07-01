<?php
session_start();
include('../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$id = isset($_POST['id']) ? $_POST['id'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;

if (!$id || !$type) {
    echo json_encode(["message" => "Error: Missing parameters"]);
    exit;
}

// Update the 'sync' column if 'type' is 'sync'
if ($type == 'sync') {
    $sql = 'UPDATE loading SET sync = 1 WHERE transaction_id = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute(array($id));
}

try {
    // Prepare and execute the SQL query
    $result = $db->prepare("SELECT transaction_id AS loading_id, helper1 AS helper1_id, helper2 AS helper2_id, driver AS driver_id, root_id, lorry_id, lorry_no FROM loading WHERE transaction_id = :id  ");
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
