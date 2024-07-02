<?php
session_start();
include('../../connect.php');
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

try {

    // Get customer the 'type' is 'find'
    if ($type == 'find') {

        $result = $db->prepare("SELECT customer_id AS id, customer_name AS name,  price_12 AS price12, price_5 AS price5, price_37 AS price37, price_2 AS price2, address, contact, area, vat_no, root_id, root FROM customer WHERE customer_id = :id ");
        $result->bindParam(':id', $id);
        $result->execute();
    } else {

        $result = $db->prepare("SELECT customer_id AS id, customer_name AS name,  price_12 AS price12, price_5 AS price5, price_37 AS price37, price_2 AS price2, address, contact, area, vat_no, root_id, root FROM customer WHERE customer_id > :id ");
        $result->bindParam(':id', $id);
        $result->execute();
    }

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
