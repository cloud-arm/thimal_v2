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
    $result = $db->prepare("SELECT customer AS customer_name, id, product_id, product_name, customer_id, n_price, price  FROM special_price  WHERE id > :id ");
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
