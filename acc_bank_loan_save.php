<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$unit = $_POST['unit'];


if ($unit == 1) {
    $loan = $_POST['loan'];
    $pay_amount = $_POST['term'];
    $pay_date = $_POST['pay_date'];
    $bank = $_POST['bank'];
    $pay_type = $_POST['pay_type'];

    $chq_no = '';
    $chq_date = '';
    $chq_bank = '';
    $invo = 'bl' . date('ymdhis');
    if ($pay_type == 'Chq') {
        $chq_no = $_POST['chq_no'];
        $chq_date = $_POST['chq_date'];
        $chq_bank = $bank;
    }

    $re = $db->prepare("SELECT * FROM bank_balance WHERE id = :id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $bank_ac = $r['ac_no'];
        $bank_name = $r['name'];
        $bank_blc = $r['amount'];
    }

    if ($pay_type == 'Chq') {
        $chq_bank = $bank_name;
    }

    $re = $db->prepare("SELECT * FROM bank_loan WHERE id = :id ");
    $re->bindParam(':id', $loan);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $loan_bank = $r['bank_name'];
        $loan_blc = $r['balance'];
    }

    $sql = "UPDATE  bank_loan SET balance = balance - ?, term_due = term_due - ?  WHERE id = ? ";
    $ql = $db->prepare($sql);
    $ql->execute(array($pay_amount, 1, $loan));

    $loan_blc = $loan_blc - $pay_amount;

    $bank_blc = $bank_blc - $pay_amount;

    $sql = "INSERT INTO bank_loan_record (amount,balance,loan_id,bank_name,bank_acc_id,bank_acc_name,bank_acc_no,bank_acc_balance,pay_date,pay_type,chq_no,chq_date,invoice_no,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($pay_amount, $loan_blc, $loan, $loan_bank, $bank, $bank_name, $bank_ac, $bank_blc, $pay_date, $pay_type, $chq_no, $chq_date, $invo, $date, $time, $user_id, $user_name));

    if ($pay_type == 'Bank') {

        $sql = "UPDATE  bank_balance SET amount = amount - ? WHERE id = ? ";
        $ql = $db->prepare($sql);
        $ql->execute(array($pay_amount, $bank));

        $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,chq_no,chq_date,chq_bank,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array('loan_payment', 'Debit', $loan, $pay_amount, 0, 0, 'loan', $loan_bank, 0, 'loan_payment', $bank_name, $bank, $bank_blc, $chq_no, $chq_date, $chq_bank, $date, $time, $user_id, $user_name));
    }

    if ($pay_type == 'Chq') {

        $amount = $pay_amount;

        $sql = 'INSERT INTO payment (amount,pay_amount,pay_type,date,invoice_no,chq_no,chq_bank,bank_id,chq_date,bank_name,type,chq_action,action,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $q = $db->prepare($sql);
        $q->execute(array($amount, $amount, $pay_type, $date, $invo, $chq_no, $chq_bank, $bank, $chq_date, '', 3, 1, 2, 'bank_loan_payment'));
    }
}

if ($unit == 2) {

    $bank_name = $_POST['bank_name'];
    $capital = $_POST['capital'];
    $interest = $_POST['interest'];
    $term = $_POST['term'];
    $term_amount = $_POST['term_amount'];

    $amount = $capital + $interest;

    $sql = "INSERT INTO bank_loan (amount,capital,interest,term,term_due,term_amount,bank_name,balance) VALUES (?,?,?,?,?,?,?,?) ";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $capital, $interest, $term, $term, $term_amount, $bank_name, $amount));

    $acc = 2;
    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id = :id");
    $re->bindParam(':id', $acc);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $acc_name = $r['name'];
    }

    $cr_blc = $blc + $capital;

    $sql = "UPDATE  cash SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($cr_blc, $acc));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('bank_loan', 'Credit', $bank_name, $capital, 0, $acc, 'bank_loan', $acc_name, $cr_blc, 'bank_loan', 'loan_payment', 0, 0, $date, $time, $user_id, $user_name));
}

$y = date("Y");
$m = date("m");
header("location: acc_bank_loan.php?year=$y&month=$m");
