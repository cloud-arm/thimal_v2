<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$ui = $_SESSION['SESS_MEMBER_ID'];
$un = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$id = $_GET['id'];

$result = $db->prepare("SELECT * FROM expenses_records WHERE id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $acc_id = $row['acc_id'];
    $amount = $row['amount'];
    $type = $row['type_id'];
    $util_id = $row['util_id'];
    $util_blc = $row['util_balance'];
    $pay_type = $row['pay_type'];
    $chq_no = $row['chq_no'];
    $invo = $row['invoice_no'];
}

if ($type == 1) {

    $sql = "UPDATE  utility_bill SET credit=credit-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($util_blc, $util_id));
}

$sql = "UPDATE  expenses_records SET dll=?, amount=? WHERE id=?";
$ql = $db->prepare($sql);
$ql->execute(array(1, 0, $id));
