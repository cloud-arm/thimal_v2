<?php
session_start();
include('connect.php');
include("config.php");
include('log/log.php');
date_default_timezone_set("Asia/Colombo");


$result = $db->prepare("SELECT * FROM customer  ");
$result->bindParam(':id', $row);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {

    $sql = "UPDATE customer SET balance = ? WHERE customer_id = ?";
    $ql = $db->prepare($sql);
    $ql->execute(array(0, $row['customer_id']));
}

$result = $db->prepare("TRUNCATE TABLE customer_record ");
$result->execute();


$result = $db->prepare("SELECT * FROM payment WHERE customer_id > 0 AND amount > 0 AND dll = 0 ORDER BY transaction_id ");
$result->bindParam(':id', $row);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $invoice = $row['invoice_no'];


    $result0 = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id AND action < 5 ");
    $result0->bindParam(':id', $invoice);
    $result0->execute();
    for ($i = 0; $row0 = $result0->fetch(); $i++) {
        $id = $row0['transaction_id'];
        $invoice = $row0['invoice_number'];
        $cus = $row0['customer_id'];
        $date = $row0['date'];
        $time = $row0['time'];
        $amount = $row0['amount'];
        $driver = $row0['cashier'];
        $driver_name = $row0['rep'];

        $sql = "UPDATE payment SET sales_id=?  WHERE invoice_no=?  AND sales_id = 0 ";
        $ql = $db->prepare($sql);
        $ql->execute(array($id, $invoice));

        $sql = "UPDATE customer_record SET sales_id=?  WHERE invoice_no=?  AND sales_id = 0 ";
        $ql = $db->prepare($sql);
        $ql->execute(array($id, $invoice));

        //get customer balance
        $con = 0;
        $result1 = $db->prepare("SELECT * FROM customer_record WHERE customer_id = :id AND sales_id = '$id' AND invoice_no = '$invoice' AND type = 'debit' ");
        $result1->bindParam(':id', $cus);
        $result1->execute();
        for ($k = 0; $row1 = $result1->fetch(); $k++) {
            $con = $row1['id'];
        }

        if ($con == 0) {

            $sql = "UPDATE customer SET balance = balance - ? WHERE customer_id = ?";
            $ql = $db->prepare($sql);
            $ql->execute(array($amount, $cus));

            //get customer balance
            $cus_balance = 0;
            $result1 = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
            $result1->bindParam(':id', $cus);
            $result1->execute();
            for ($k = 0; $row1 = $result1->fetch(); $k++) {
                $cus_balance = $row1['balance'];
            }

            // insert customer record
            $sql = "INSERT INTO customer_record (invoice_no,type,date,time,debit,balance,sales_id,customer_id,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, 'debit', $date, $time, $amount, $cus_balance, $id, $cus, $driver, $driver_name));
        }
    }

    $cus = $row['customer_id'];
    $date = $row['date'];
    $time = $row['time'];
    $amount = $row['amount'];
    $pay_type = $row['pay_type'];
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $pay_id = $row['transaction_id'];
    $load = $row['loading_id'];

    if ($pay_type == 'credit') {
    } else {

        // get loading
        $driver = 0;
        $driver_name = 0;
        $result1 = $db->prepare("SELECT * FROM loading WHERE transaction_id = :id  ");
        $result1->bindParam(':id', $load);
        $result1->execute();
        for ($k = 0; $row1 = $result1->fetch(); $k++) {
            $driver = $row1['driver'];
            $driver_name = $row1['rep'];
        }

        // get sales
        $sales_id = 0;
        $result1 = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
        $result1->bindParam(':id', $invoice);
        $result1->execute();
        for ($k = 0; $row1 = $result1->fetch(); $k++) {
            $sales_id = $row1['transaction_id'];
        }

        //update customer balance
        $sql = "UPDATE customer SET balance = balance + ? WHERE customer_id = ?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $cus));

        //get customer balance
        $cus_balance = 0;
        $result1 = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
        $result1->bindParam(':id', $cus);
        $result1->execute();
        for ($k = 0; $row1 = $result1->fetch(); $k++) {
            $cus_balance = $row1['balance'];
        }

        // insert customer record
        $sql = "INSERT INTO customer_record (invoice_no,type,date,pay_type,chq_no,chq_date,time,credit,balance,sales_id,customer_id,user_id,user_name,pay_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($invoice, 'credit', $date, $pay_type, $chq_no, $chq_date, $time, $amount, $cus_balance, $sales_id, $cus, $driver, $driver_name, $pay_id));
    }
}

$return = $_SESSION['SESS_BACK'];

header("location: $return");
