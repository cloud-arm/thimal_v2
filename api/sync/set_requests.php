<?php
include('../../connect.php');
include('../../config_sync.php');
include('../../log/log.php');
include("../../config.php");
date_default_timezone_set("Asia/Colombo");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$requests = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($requests as $list) {

    // get values
    $note = $list['note'];
    $date = $list['date'];
    $time = $list['time'];
    $type = $list['type'];
    $user_id = $list['user_id'];
    $cus = $list['customer_id'];
    $app_id = $list['id'];


    // get customer details
    $cus_name = null;
    $result = $db->prepare("SELECT * FROM customer WHERE customer_id=:id  ");
    $result->bindParam(':id', $cus);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $cus_name = $row['customer_name'];
    }

    //------------------------------------------------------------------//
    try {

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM requests_data WHERE customer_id = :id AND app_id = '$app_id' AND action = 0 ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['id'];
        }

        if ($con == 0) {

            // insert query
            // $sql = "INSERT INTO requests_data (type,customer_id,customer_name,note,date,time,user_id,app_id) VALUES (?,?,?,?,?,?,?,?)";
            // $ql = $db->prepare($sql);
            // $ql->execute(array($type, $cus, $cus_name, $note, $date, $time, $user_id, $app_id));

            $insertData = array(
                "data" => array(
                    "type" => $type,
                    "date" => $date,
                    "time" => $time,
                    "customer_id" => $cus,
                    "customer_name" => $cus_name,
                    "note" => $note,
                    "user_id" => $user_id,
                    "app_id" => $app_id,
                ),
                "other" => array(
                    "data_id" => $cus,
                    "data_name" => "customer",
                ),
            );

            $status = insert($db, "requests_data", $insertData, '../../', 'set_requests.php');
        } else {
            $status = array(
                "status" => "success",
                "message" => "Already included",
            );
        }

        // get sales  data
        $id = 0;
        $ap_id = 0;
        $result = $db->prepare("SELECT * FROM requests_data WHERE customer_id=:id AND action = 0  ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['id'];
            $ap_id = $row['app_id'];
        }

        // create success respond 
        $res = array(
            "cloud_id" => $id,
            "app_id" => $ap_id,
            "status" => $status['status'],
            "message" => $status['message'],
        );

        array_push($result_array, $res);
    } catch (PDOException $e) {

        // create error respond 
        $res = array(
            "cloud_id" => 0,
            "app_id" => 0,
            "status" => "failed",
            "message" => $e->getMessage(),
        );

        array_push($result_array, $res);

        // Create log
        $content = "cloud_id: 0, app_id: " . $app_id . ", status: failed, message: " . $e->getMessage() . ", Date: " . date('Y-m-d') . ", Time: " . date('H:s:i');
        log_init('requests_data', $content, 'txt', '../../');
    }
}

// send respond
echo (json_encode($result_array));
