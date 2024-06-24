<?php
session_start();
include('config.php');
include('sub_class/cash_transaction.php');
include('sub_class/bank_transaction.php');
date_default_timezone_set("Asia/Colombo");


$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$record_no = $_POST['id'];

//
$result =  select("acc_transaction_record", "*", "record_no = '" . $record_no . "' ");
foreach ($result as $row) {

    if ($row['type'] == 'Credit') {
        $amount = $row['credit'];
    }
    if ($row['type'] == 'Debit') {
        $amount = $row['debit'];
    }

    if ($row['table_id']) {

        if ($row['table_name'] == 'cash') {
            $status = cash_transaction($row['table_id'], $row['type'], $amount, "acc_transaction", $record_no);
            echo '<br>Line: 32 ' .  $status['status'] . ' / ' . $status['message'];
        }

        if ($row['table_name'] == 'bank') {
            $status = bank_transaction($row['table_id'], $row['type'], $amount, "acc_transaction", $record_no);
            echo '<br>Line: 37 ' .  $status['status'] . ' / ' . $status['message'];
        }
    } else {
    }

    // update transaction
    echo 'Line: 43 ' . query("UPDATE acc_transaction_record  SET action = 1  WHERE transaction_id = '" . $row['transaction_id'] . "' ");
}



header("location: acc_transaction.php");
