<?php
session_start();
include('connect.php');

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$type = $_POST['type'];


if ($type == 'fund') {
    $from = $_POST['acc_from'];
    $to = $_POST['acc_to'];
    $amount = $_POST['amount'];

    $load = 0;
    if ($from == 2) {
        $load = $_POST['load'];

        $sql = "UPDATE  loading SET cash_balance = cash_balance - ? WHERE transaction_id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $load));
    }

    $re_no = 0;
    $cr_type = 'cash_transfer';
    $de_type = 'cash_transfer';
    $tr_type = 'acc_transfer';

    account_transfer($from, $to, $tr_type, $re_no, $amount, $cr_type, $de_type, $load);
}

function bank_transfer($from, $to, $tr_type, $re_no, $amount, $cr_type, $de_type)
{
    include('connect.php');
    $user_id = $_SESSION['SESS_MEMBER_ID'];
    $user_name = $_SESSION['SESS_FIRST_NAME'];
    $date = date("Y-m-d");
    $time = date('H:i:s');


    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id=:id ");
    $re->bindParam(':id', $from);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $cr_blc = $blc - $amount;

    $de_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id= :id ");
    $re->bindParam(':id', $to);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $de_name = $r['name'];
    }

    $de_blc = $blc + $amount;

    $sql = "UPDATE  bank_balance SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($de_blc, $to));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($tr_type, 'Debit', $re_no, $amount, 0, $to, $de_type, $de_name, $de_blc, $cr_type, $cr_name, $from, $cr_blc, $date, $time, $user_id, $user_name));

    $sql = "UPDATE  bank_balance SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($cr_blc, $from));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($tr_type, 'Credit', $re_no, $amount, 0, $to, $de_type, $de_name, $de_blc, $cr_type, $cr_name, $from, $cr_blc, $date, $time, $user_id, $user_name));
}

function account_transfer($from, $to, $tr_type, $re_no, $amount, $cr_type, $de_type, $load)
{
    include('connect.php');
    $user_id = $_SESSION['SESS_MEMBER_ID'];
    $user_name = $_SESSION['SESS_FIRST_NAME'];
    $date = date("Y-m-d");
    $time = date('H:i:s');


    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id=:id ");
    $re->bindParam(':id', $from);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $cr_blc = $blc - $amount;

    $de_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id= :id ");
    $re->bindParam(':id', $to);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $de_name = $r['name'];
    }

    $de_blc = $blc + $amount;

    $sql = "UPDATE  cash SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($cr_blc, $from));

    $sql = "UPDATE  cash SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($de_blc, $to));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,loading_id,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($tr_type, 'Debit', $re_no, $load, $amount, 0, $to, $de_type, $de_name, $de_blc, $cr_type, $cr_name, $from, $cr_blc, $date, $time, $user_id, $user_name));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,loading_id,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($tr_type, 'Credit', $re_no, $load, $amount, 0, $to, $de_type, $de_name, $de_blc, $cr_type, $cr_name, $from, $cr_blc, $date, $time, $user_id, $user_name));
}

header("location: acc_transfer.php");
