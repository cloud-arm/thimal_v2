<?php
include('../../connect.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$unloading = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($unloading as $list) {

    // get values
    $app_id = $list['id'];
    $load = $list['loading_id'];
    $driver = $list['driver_id'];
    $r5000 = $list['r5000'];
    $r1000 = $list['r1000'];
    $r500 = $list['r500'];
    $r100 = $list['r100'];
    $r50 = $list['r50'];
    $r20 = $list['r20'];
    $r10 = $list['r10'];
    $coins = $list['coins'];
    $cash_amount = $list['cash_amount'];


    //------------------------------------------------------------------//
    try {

        // unloading
        $sql = "UPDATE loading SET  r5000 = ?, r1000 = ?, r500 = ?, r100 = ?, r50 = ?, r20 = ?, r10 = ?, coins = ?, cash_total = ? WHERE transaction_id = ? AND driver = ?";
        $q = $db->prepare($sql);
        $q->execute(array( $r5000, $r1000, $r500, $r100, $r50, $r20, $r10, $coins, $cash_amount, $load, $driver));

        // create success respond 
        $res = array(
            "status" => "success",
            "message" => "",
        );

        array_push($result_array, $res);
    } catch (PDOException $e) {

        // create error respond 
        $res = array(
            "status" => "failed",
            "message" => $e->getMessage(),
        );

        array_push($result_array, $res);
    }
}

// send respond
echo (json_encode($result_array));
