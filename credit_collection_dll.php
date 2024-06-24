<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$pay_id = $_POST['id'];

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$result = $db->prepare("SELECT * FROM payment WHERE  transaction_id=:id  ");
$result->bindParam(':id', $pay_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $amount = $row['amount'];
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $chq_bank = $row['chq_bank'];
    $bank = $row['bank_id'];
    $pay_type = $row['pay_type'];
    $invoice_no = $row['invoice_no'];
    $customer_id = $row['customer_id'];
    $id = $row['collection_id'];
}

$sql = "UPDATE payment SET action = ?, dll = ? WHERE transaction_id = ?";
$ql = $db->prepare($sql);
$ql->execute(array(11, 1, $pay_id));

$sql = "UPDATE credit_payment SET action=?, dll=? WHERE pay_id=?";
$q = $db->prepare($sql);
$q->execute(array(5, 1, $pay_id));

$sql = "UPDATE collection SET action=?, dll=? WHERE id=?";
$q = $db->prepare($sql);
$q->execute(array(5, 1, $id));

$result = $db->prepare("SELECT * FROM payment WHERE  credit_pay_id=:id AND pay_type = 'credit_payment' ");
$result->bindParam(':id', $pay_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $row_id = $row['transaction_id'];
    $credit_id = $row['credit_id'];
    $pay_amount = $row['amount'];
    $customer_id = $row['customer_id'];
    $invoice_no = $row['invoice_no'];
    $pay_type1 = $row['pay_type'];
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $sales_id = $row['sales_id'];

    $sql = "UPDATE payment SET pay_amount = pay_amount - ?, credit_balance = credit_balance + ?, set_off_date = '' WHERE transaction_id = ? ";
    $q = $db->prepare($sql);
    $q->execute(array($pay_amount, $pay_amount, $credit_id));

    $sql = "UPDATE payment SET action = ?, dll = ? WHERE transaction_id = ?";
    $ql = $db->prepare($sql);
    $ql->execute(array(11, 1, $row_id));

    //update customer balance
    $sql = "UPDATE customer SET balance = balance - ? WHERE customer_id = ?";
    $ql = $db->prepare($sql);
    $ql->execute(array($pay_amount, $customer_id));

    //get customer balance
    $result1 = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
    $result1->bindParam(':id', $customer_id);
    $result1->execute();
    for ($i = 0; $row1 = $result1->fetch(); $i++) {
        $cus_balance = $row1['balance'];
    }

    // insert customer record
    $sql = "INSERT INTO customer_record (invoice_no,type,date,pay_type,chq_no,chq_date,time,credit,balance,sales_id,customer_id,user_id,user_name,pay_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($invoice_no, 'debit', $date, $pay_type1, $chq_no, $chq_date, $time, $pay_amount, $cus_balance, $sales_id, $customer_id, $user_id, $user_name, $row_id));
}

if ($pay_type == 'cash') {

    $cr_id = 2;
    $blc = 0;

    $sql = "UPDATE  cash SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $cr_id));

    $re = $db->prepare("SELECT * FROM cash WHERE id= :id ");
    $re->bindParam(':id', $cr_id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('credit_collection_delete', 'Debit', $pay_id, $amount, 0, 0, 'credit_payment', 'Payment Delete', 0, 'credit_payment_delete', $cr_name, $cr_id, $blc, $date, $time, $user_id, $user_name));
}

if ($pay_type == 'bank') {

    $blc = 0;

    $sql = "UPDATE  bank_balance SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $re = $db->prepare("SELECT * FROM bank_balance WHERE id=:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $de_name = $r['name'];
    }

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('credit_collection_delete', 'Debit', $pay_id, $amount, 0, 0, 'credit_payment', 'Payment Delete', 0, 'credit_payment_delete', $de_name, $bank, $blc, $date, $time, $user_id, $user_name));
}

$url = $_SESSION['SESS_BACK'];

header("location: $url");
