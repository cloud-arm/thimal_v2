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
    echo json_encode(array("message" => "No transaction ID provided."));
    exit();
}

$result_array = [];

// Define the employee types and corresponding columns
$employee_types = [
    "Driver" => "driver",
    "Helper1" => "helper1",
    "Helper2" => "helper2",
    "Helper3" => "helper3",
];

foreach ($employee_types as $type => $column) {
    $query = "SELECT loading.transaction_id, loading.action, employee.id AS emp_id, employee.name 
              FROM loading 
              JOIN employee ON loading.$column = employee.id 
              WHERE loading.transaction_id = :id AND loading.$column > 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result_array[] = array(
            "id" => $row['transaction_id'],
            "action" => $row['action'],
            "type" => $type,
            "emp_id" => $row['emp_id'],
            "name" => $row['name'],
        );
    }
}

echo json_encode($result_array);
