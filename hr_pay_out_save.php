<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$ui = $_SESSION['SESS_MEMBER_ID'];
$un = $_SESSION['SESS_FIRST_NAME'];

$date = date('Y-m-d');
$time = date('H:i:s');
$year = date('Y');
$month = date('m');

$invo = "etf" . date("ymdhis");

$type = $_POST['type'];

if ($type == 'salary') {

    $id = $_POST['id'];
    $amount = $_POST['payment'];
    $month = $_POST['month'];

    $sql = 'UPDATE  hr_payroll SET payment = payment + ?, pay_date = ? WHERE id =? ';
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $date, $id));

    $re = $db->prepare("SELECT * FROM hr_payroll WHERE id = :id");
    $re->bindParam(':id', $id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $emp = $r['emp_id'];
    }

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

    $cr_blc = $blc - $amount;

    $sql = "UPDATE  cash SET amount=? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($cr_blc, $acc));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('hr_payment', 'Debit', $emp, $amount, 0, 0, 'salary', 'Emp Salary', 0,'salary_payment', $acc_name, $acc, $cr_blc, $date, $time, $ui, $un));
}

if ($type == 'etf') {

    $month = $_POST['month'];
    $hr_type = $_POST['hr_type'];
    $pay_type = $_POST['pay_type'];
    $chq_no = '';
    $chq_date = '';
    $bank = '';
    $bank_name = '';

    $type = $hr_type . '_pay';

    $result = $db->prepare("SELECT sum($hr_type) FROM hr_payroll WHERE  $type = '0' AND date = '$month' ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $hr_blc = $row['sum(' . $hr_type . ')'];
    }

    $result = $db->prepare("SELECT sum($hr_type) FROM hr_payroll WHERE  $type = '0'  ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $tot_hr_blc = $row['sum(' . $hr_type . ')'];
    }

    $acc = 1;
    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id = :id");
    $re->bindParam(':id', $acc);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $acc_name = $r['name'];
    }

    if ($pay_type == 'Chq') {
        $re = $db->prepare("SELECT * FROM bank_balance WHERE id = :id");
        $re->bindParam(':id', $_POST['bank']);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $bank = $r['id'];
            $bank_name = $r['name'];
        }
    }


    $amount = $hr_blc;

    $de_blc = $tot_hr_blc - $amount;

    if ($pay_type == 'Cash') {

        $cr_blc = $blc - $amount;

        $sql = "UPDATE  cash SET amount=? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($cr_blc, $acc));

        $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array('hr_payment', 'Debit', $month, $amount, 0, 0, 'cash_payment', $hr_type . ' payment', 0, $hr_type . '_payment', $acc_name, $acc, $cr_blc, $date, $time, $ui, $un));
    }

    if ($pay_type == 'Chq') {

        $chq_no = $_POST['chq_no'];
        $chq_date = $_POST['chq_date'];

        $sql = 'INSERT INTO payment (amount,pay_amount,pay_type,date,invoice_no,job_id,cus_id,vehicle_id,customer_name,chq_no,chq_bank,bank_id,chq_date,bank_name,type,action,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $q = $db->prepare($sql);
        $q->execute(array($amount, $amount, $pay_type, $date, $invo, 0, 0, 0, '', $chq_no, $bank_name, $bank, $chq_date, '', 3, 1, 'hr_payment'));
    }

    $sql = "INSERT INTO hr_etf_record (type,amount,froward_balance,month,acc,acc_name,date,time,chq_no,chq_bank,bank_id,chq_date,invoice_no,pay_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($hr_type, $amount, $de_blc, $month, $acc,  $acc_name, $date, $time, $chq_no, $bank_name, $bank, $chq_date, $invo, $pay_type));

    $sql = "UPDATE  hr_payroll SET $type = ? WHERE date =?";
    $ql = $db->prepare($sql);
    $ql->execute(array(1, $month));
}

header("location: hr_pay_out.php?year=$year&month=$month");
