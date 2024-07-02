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
    // Fetch the results and create an array
    $result_array = array();
    $result = $db->prepare("SELECT * FROM payment JOIN customer ON payment.customer_id=customer.customer_id WHERE payment.type='credit' AND payment.pay_amount < payment.amount AND payment.transaction_id > :id AND payment.action > 0 AND payment.dll = 0 ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $result_array[] = array(
            "name" => $row['customer_name'],
            "cus_id" => $row['customer_id'],
            "amount" => $row['amount'],
            "balance" => $row['amount'] - $row['pay_amount'],
            "type" => $row['type'],
            "invoice_no" => $row['invoice_no'],
            "date" => $row['date'],
            "id" => $row['transaction_id'],
        );
    }

    // Encode the array into JSON and output it
    echo json_encode($result_array);
} catch (PDOException $e) {
    echo json_encode(array("message" => "Error: " . $e->getMessage()));
}
