<?php
include("config.php");
date_default_timezone_set("Asia/Colombo");

$unit  = $_GET['unit'];

if ($unit == 1) {
    $main_id = $_GET['val'];

    echo sprintf('<option value="0" disabled selected>Select Sub Type</option>');

    $result = select("acc_account_type", "acc_name,sn", "group_name = 'assets' AND type = 'sub1' AND main_id = '" . $main_id . "' ");
    foreach ($result as $row) {
        echo sprintf('<option value="%s"> %s </option>', $row['sn'], $row['acc_name']);
    }
}

if ($unit == 2) {
    $val = $_GET['val'];

    echo sprintf('<option value="0" disabled selected>Select Account Type</option>');

    if ($val == 'expenses') {
        $result = select("expenses_types", "type_name,sn");
        foreach ($result as $row) {
            echo sprintf('<option value="%s"> %s </option>', $row['sn'], $row['type_name']);
        }
    } else {
        $result = select("acc_account", "acc_name,sn", "type = 'main' AND group_name = '" . $val . "' ");
        foreach ($result as $row) {
            echo sprintf('<option value="%s"> %s </option>', $row['sn'], $row['acc_name']);
        }
    }
}

if ($unit == 3) {
    $val = $_GET['val'];

    echo sprintf('<option value="0" disabled selected>Select Account</option>');

    $result = select($val, "name,id");
    foreach ($result as $row) {
        echo sprintf('<option value="%s"> %s </option>', $row['id'], $row['name']);
    }
}
