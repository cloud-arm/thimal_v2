<?php
session_start();
include('config.php');
include('sub_class/acc_transaction.php');
date_default_timezone_set("Asia/Colombo");

$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$record_no = $_POST['id'];
$account = $_POST['account'];
$type = $_POST['type'];
$amount = $_POST['amount'];
$memo = $_POST['memo'];

$result = select("acc_account", "source_id,source_table", "id = '" . $account . "' ");
foreach ($result as $row) {
    $table_id = $row['source_id'];
    $table_name = $row['source_table'];
}


$status = acc_transaction($account, $type, $amount, "acc_transaction", $record_no, 0, $memo, 0, ["id" => $table_id, "name" => $table_name]);
echo '<br>Line: 25 ' .  $status['status'] . ' / ' . $status['message'];



$id = base64_encode($record_no);
header("location: acc_transaction.php?id=$id");
