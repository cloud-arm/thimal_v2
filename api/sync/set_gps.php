<?php
include('../../connect.php');
include('../../config.php');
date_default_timezone_set("Asia/Colombo");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$date = date("Y-m-d");
$time = date('H:i:s');

$id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$lat = isset($_POST['lat']) ? $_POST['lat'] : null;
$lng = isset($_POST['lng']) ? $_POST['lng'] : null;

if (!$id || !$lat || !$lng) {
    // create error respond 
    $result_array[] = array(
        "status" => "failed",
        "message" => "Error: Missing parameters",
        "action" => 0,
    );

    echo (json_encode($result_array));
    exit;
}

// respond init
$result_array = array();

$load_id = 0;
$result = $db->prepare("SELECT * FROM loading WHERE  driver ='$id' AND action='load' ");
$result->bindParam(':id', $res);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $load_id = $row['transaction_id'];
}



//------------------------------------------------------------------//
try {

    $updateData = array(
        "lat" => $lat,
        "lng" => $lng,
        "date" => $date,
        "time" => $time,
    );

    $status =  update("user", $updateData, "EmployeeId = " . $id, "../../");

    $result_array[] = array(
        "status" => $status['status'],
        "message" => $status['message'],
        "action" => $load_id,
    );
} catch (PDOException $e) {

    // create error respond 
    $result_array[] = array(
        "status" => "failed",
        "message" => $e->getMessage(),
        "action" => $load_id,
    );
}


// send respond
echo (json_encode($result_array));
