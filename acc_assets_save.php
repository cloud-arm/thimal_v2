<?php
session_start();
include('config.php');
date_default_timezone_set("Asia/Colombo");

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$acc_type = $_POST['acc_type'];
$sub_type = $_POST['sub_type'];
$name = $_POST['name'];
$dep_month = $_POST['dep_month'];
$dep_amount = $_POST['dep_amount'];


$result = select("acc_account_type", "acc_name", "sn = '" . $acc_type . "' ");
foreach ($result as $row) {
    $acc_type_name = $row['acc_name'];
}

$result = select("acc_account_type", "acc_name", "sn = '" . $sub_type . "' ");
foreach ($result as $row) {
    $sub_type_name = $row['acc_name'];
}

if ($sub_type == 6) {

    $insertData = array(
        "data" => array(
            "acc_name" => $name,
            "main_id" => $acc_type,
            "type" => $acc_type_name,
            "sub_id" => $sub_type,
            "sub_type" => $sub_type_name,
            "dep_month" => $dep_month,
            "dep_amount" => $dep_amount,
            "date" => $date,
        ),
        "other" => array(
            "data_id" => $acc_type,
            "data_name" => "Account",
        ),
    );

    if (!empty($name)) {
        $status = insert("acc_assets", $insertData);
        echo '<br>Line: 50 ' .  $status['status'] . ' / ' . $status['message'];
    }

    $assets_id = 0;
    $result = select("acc_assets", "id", "acc_name = '" . $name . "' ORDER BY `acc_assets`.`id` DESC LIMIT 1 ");
    foreach ($result as $row) {
        $assets_id = $row['id'];
    }

    $insertData = array(
        "data" => array(
            "name" => $name,
            "type" => $sub_type_name,
            "type_id" => $sub_type,
            "balance" => 0,
            "source_table" => "acc_assets",
            "source_id" => $assets_id,
        ),
        "other" => array(
            "data_id" => $acc_type,
            "data_name" => "Account",
        ),
    );

    if (!empty($name)) {
        $status = insert("acc_account", $insertData);
        echo '<br>Line: 75 ' .  $status['status'] . ' / ' . $status['message'];
    }
}

if ($sub_type == 5) {

    $lorry_no = $_POST['lorry_no'];
    $driver = $_POST['driver'];

    $name = '';
    $result = select("employee", "name", "id = '" . $driver . "'  ");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $name = $row['name'];
    }

    $user = 0;
    $result = select("user", "id", "EmployeeId = '" . $driver . "'  ");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $user = $row['id'];
    }

    $insertData = array(
        "data" => array(
            "lorry_no" => $lorry_no,
            "driver" => $name,
            "driver_id" => $driver,
            "action" => "unload",
            "user_id" => $user,
        ),
        "other" => array(
            "data_id" => $acc_type,
            "data_name" => "Account",
        ),
    );

    if (!empty($lorry_no)) {
        $status = insert("lorry", $insertData);
        echo '<br>Line: 110 ' .  $status['status'] . ' / ' . $status['message'];
    }

    $lorry_id = 0;
    $result = select("lorry", "lorry_id", "lorry_no = '" . $lorry_no . "' ");
    foreach ($result as $row) {
        $lorry_id = $row['lorry_id'];
    }

    $insertData = array(
        "data" => array(
            "name" => $lorry_no,
            "type" => $sub_type_name,
            "type_id" => $sub_type,
            "balance" => 0,
            "source_table" => "lorry",
            "source_id" => $lorry_id,
        ),
        "other" => array(
            "data_id" => $acc_type,
            "data_name" => "Account",
        ),
    );

    if (!empty($lorry_no)) {
        $status = insert("acc_account", $insertData);
        echo '<br>Line: 140 ' .  $status['status'] . ' / ' . $status['message'];
    }
}

header("location: acc_account.php?assets");
