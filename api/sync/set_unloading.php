<?php
include('../../connect.php');
include("../../config.php");
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
        $updateData = array(
            "r5000" => $r5000,
            "r1000" => $r1000,
            "r500" => $r500,
            "r100" => $r100,
            "r50" => $r50,
            "r20" => $r20,
            "r10" => $r10,
            "coins" => $coins,
            "cash_total" => $cash_amount,
        );

        $result_array[] =  update("loading", $updateData, "transaction_id = " . $load . " AND driver = " . $driver, "../../");
    } catch (PDOException $e) {

        // create error respond 
        $result_array[] = array(
            "status" => "failed",
            "message" => $e->getMessage(),
        );
    }
}

// send respond
echo (json_encode($result_array));
