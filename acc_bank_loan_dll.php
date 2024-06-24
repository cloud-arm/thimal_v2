<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$id = $_GET['id'];

$result = $db->prepare("SELECT * FROM bank_loan_record WHERE id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $loan_id = $row['loan_id'];
    $amount = $row['amount'];
    $loan_bank = $row['bank_name'];
    $bank_id = $row['bank_acc_id'];
    $bank_name = $row['bank_acc_name'];
}

$re = $db->prepare("SELECT * FROM bank_balance WHERE id = :id ");
$re->bindParam(':id', $bank_id);
$re->execute();
for ($i = 0; $r = $re->fetch(); $i++) {
    $bank_blc = $r['amount'];
}

$re = $db->prepare("SELECT * FROM bank_loan WHERE id = :id ");
$re->bindParam(':id', $loan_id);
$re->execute();
for ($i = 0; $r = $re->fetch(); $i++) {
    $loan_blc = $r['balance'];
}

$sql = "UPDATE  bank_loan SET balance = balance + ?, term_due = term_due + ? WHERE id = ? ";
$ql = $db->prepare($sql);
$ql->execute(array($amount, 1, $loan_id));

$sql = "UPDATE  bank_balance SET amount = amount + ? WHERE id = ? ";
$ql = $db->prepare($sql);
$ql->execute(array($amount, $bank_id));

$loan_blc = $loan_blc + $amount;

$bank_blc = $bank_blc + $amount;

$sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$ql = $db->prepare($sql);
$ql->execute(array('loan_payment_delete', 'Credit', $id, $amount, 0, $bank_id, 'cash_transfer', $bank_name, $bank_blc, 'cash_transfer', $loan_bank, 0, 0, $date, $time, $user_id, $user_name));

$sql = "UPDATE  bank_loan_record SET dll=?, amount=? WHERE id=?";
$ql = $db->prepare($sql);
$ql->execute(array(1, 0, $id));
