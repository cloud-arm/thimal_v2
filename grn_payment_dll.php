<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$ui = $_SESSION['SESS_MEMBER_ID'];
$un = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$id = $_GET['id'];

$result = $db->prepare("SELECT * FROM supply_payment WHERE id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $invo = $row['invoice_no'];
    $pay_type = $row['pay_type'];
    $amount = $row['pay_amount'];
}

if ($pay_type == 'Cash') {

    $cr_id = 2;

    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id='$cr_id' ");
    $re->bindParam(':userid', $res);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $cr_blc = $blc + $amount;

    $sql = "UPDATE  cash SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($cr_blc, $cr_id));

    $cr_type = 'payment_delete';

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('grn_payment_delete', 'Credit', $invo, $amount, 0, $cr_id, $cr_type, $cr_name, $cr_blc, $cr_type, 'GRN Payment', $id, 0, $date, $time, $ui, $un));
}


$sql = "UPDATE  supply_payment SET dll=?, pay_amount=? WHERE id=?";
$ql = $db->prepare($sql);
$ql->execute(array(1, 0, $id));

$cr_blc = 0;
$blc = 0;
$re = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=$invo AND pay_type='Credit' ");
$re->bindParam(':id', $invo);
$re->execute();
for ($k = 0; $r = $re->fetch(); $k++) {
    $blc = $r['credit_balance'];
    $cr_id = $r['id'];
}

$cr_blc = $blc + $amount;

$sql = "UPDATE supply_payment SET credit_balance=? WHERE id=?";
$ql = $db->prepare($sql);
$ql->execute(array($cr_blc, $cr_id));

header("location: grn_payment.php");
