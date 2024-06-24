<?php
session_start();
include('config.php');
date_default_timezone_set("Asia/Colombo");

$unit = $_POST['unit'];

if ($unit == 1) {

    $type = $_POST['type'];

    $name = trim($type);
    $name = ucwords($name);
    $name = str_replace(" ", "_", $name);

    $id = 0;
    $result = select("expenses_types", "sn", "type_name= '" . $name . "' ");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $id = $row['sn'];
    }

    if ($id == 0) {

        $insertData = array(
            "data" => array(
                "type_name" => $name,
            ),
            "other" => array(
                "data_id" => $id,
                "data_name" => "Account_type",
            ),
        );

        if (!empty($name)) {
            $status = insert("expenses_types", $insertData);
            echo '<br>Line: 35 ' .  $status['status'] . ' / ' . $status['message'];
        }

        $insertData = array(
            "data" => array(
                "acc_name" => $name,
                "group_name" => "expenses",
                "type" => "main",
                "child_table" => "expenses_types",
                "data_source" => "child_table",
            ),
            "other" => array(
                "data_id" => 0,
                "data_name" => "Account_type",
            ),
        );

        if (!empty($name)) {
            $status = insert("acc_account_type", $insertData);
            echo '<br>Line: 55 ' .  $status['status'] . ' / ' . $status['message'];
        }
    }

    header("location: acc_account.php?expenses");
}

if ($unit == 2) {

    $acc_type = $_POST['acc_type'];
    $acc_name = $_POST['acc_name'];


    $name = trim($acc_name);
    $name = ucwords($name);
    $name = str_replace(" ", "_", $name);

    $type_name = '';
    $type_id = 0;
    $result = select("acc_account_type", "acc_name", "sn= '" . $acc_type . "' ");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $type_name = $row['acc_name'];
    }
    $result = select("expenses_types", "sn", "type_name= '" . $type_name . "' ");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $type_id = $row['sn'];
    }

    $id = 0;
    $result = select("expenses_sub_type", "id", "name = '" . $name . "' AND type_id = '" . $type_id . "' ");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $id = $row['id'];
    }

    if ($id == 0) {

        $insertData = array(
            "data" => array(
                "name" => $name,
                "type_id" => $type_id,
                "type" => $type_name,
            ),
            "other" => array(
                "data_id" => $id,
                "data_name" => "Account",
            ),
        );

        if (!empty($name)) {
            $status = insert("expenses_sub_type", $insertData);
            echo '<br>Line: 105 ' .  $status['status'] . ' / ' . $status['message'];
        }

        $sub_type_id = 0;
        $result = select("expenses_sub_type", "id", "name = '" . $name . "' AND type_id = '" . $type_id . "' ");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $sub_type_id = $row['id'];
        }

        $insertData = array(
            "data" => array(
                "name" => $name,
                "type" => $type_name,
                "type_id" => $acc_type,
                "balance" => 0,
                "source_table" => "expenses_sub_type",
                "source_id" => $sub_type_id,
            ),
            "other" => array(
                "data_id" => 0,
                "data_name" => "Account_type",
            ),
        );

        if (!empty($name)) {
            $status = insert("acc_account", $insertData);
            echo '<br>Line: 130 ' .  $status['status'] . ' / ' . $status['message'];
        }
    }

    header("location: acc_account.php?expenses=acc");
}
