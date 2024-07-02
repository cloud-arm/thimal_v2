<?php
include('../../connect.php');
include('../../config.php');
include('log.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$payment = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($payment as $list) {

    // get values
    $invoice = $list['invoice_no'];
    $amount = $list['amount'];
    $pay_type = $list['pay_type'];
    $date = $list['date'];
    $app_id = $list['id'];
    $chq_no = $list['chq_no'];
    $bank = $list['bank'];
    $chq_date = $list['chq_date'];
    $time = $list['time'];
    $load = $list['loading_id'];
    $cus = $list['cus_id'];


    // get sales
    $sales_id = 0;
    $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
    $result->bindParam(':id', $invoice);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $sales_id = $row['transaction_id'];
    }

    $action = 0;
    $credit = 0;
    $pay_amount = 0;

    if ($pay_type == 'credit' | $pay_type == 'Credit') {

        $credit = $amount;
        $pay_amount = 0;
        $action = 2;
    } else

    if ($pay_type == 'chq' | $pay_type == 'Chq') {

        $credit = 0;
        $pay_amount = 0;
        $action = 2;
    } else {

        $credit = 0;
        $action = 1;
        $pay_amount = $amount;
    }

    $paycose = 'invoice_payment';
    if ($pay_type == 'discount' | $pay_type == 'Discount') {
        $paycose = 'special_payment';
    }

    //------------------------------------------------------------------//
    try {

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM payment WHERE invoice_no = '$invoice' AND app_id = '$app_id' AND loading_id = '$load' ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['transaction_id'];
        }

        if ($con == 0) {

            // insert query
            $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,chq_no,bank_name,chq_date,chq_action,action,sales_id,customer_id,loading_id,pay_type,chq_bank,credit_balance,app_id,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, $pay_amount, $amount, $pay_type, $date, $time, $chq_no, '', $chq_date, 0, $action, $sales_id, $cus, $load, $pay_type, $bank, $credit, $app_id, $paycose));

            if ($pay_type == 'credit' | $pay_type == 'Credit') {
            } else {

                // get loading
                $driver = 0;
                $driver_name = 0;
                $result = $db->prepare("SELECT * FROM loading WHERE transaction_id = :id  ");
                $result->bindParam(':id', $load);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $driver = $row['driver'];
                    $driver_name = $row['rep'];
                }

                // get sales
                $sales_id = 0;
                $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
                $result->bindParam(':id', $invoice);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $sales_id = $row['transaction_id'];
                }

                // get payment
                $pay_id = 0;
                $result = $db->prepare("SELECT * FROM payment WHERE invoice_no = :id ORDER BY `transaction_id` DESC LIMIT 1 ");
                $result->bindParam(':id', $invoice);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $pay_id = $row['transaction_id'];
                }

                //update customer balance
                $sql = "UPDATE customer SET balance = balance + ? WHERE customer_id = ?";
                $ql = $db->prepare($sql);
                $ql->execute(array($amount, $cus));

                //get customer balance
                $result = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
                $result->bindParam(':id', $cus);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $cus_balance = $row['balance'];
                }

                // insert customer record
                $sql = "INSERT INTO customer_record (invoice_no,type,date,pay_type,chq_no,chq_date,time,credit,balance,sales_id,customer_id,user_id,user_name,pay_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $ql = $db->prepare($sql);
                $ql->execute(array($invoice, 'credit', $date, $pay_type, $chq_no, $chq_date, $time, $amount, $cus_balance, $sales_id, $cus, $driver, $driver_name, $pay_id));
            }

            // get sales
            $sales_id = 0;
            $sales_amount = 0;
            $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $sales_id = $row['transaction_id'];
                $sales_amount = $row['amount'];
            }

            // get payment
            $payment_amount = 0;
            $result = $db->prepare("SELECT sum(amount) FROM payment WHERE invoice_no = :id  ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $payment_amount = $row['sum(amount)'];
            }

            // get payment
            $credit_amount = 0;
            $result = $db->prepare("SELECT sum(amount) FROM payment WHERE invoice_no = :id AND pay_type = 'credit' ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $credit_amount = $row['sum(amount)'];
            }

            // get payment
            $credit_note_amount = 0;
            $result = $db->prepare("SELECT sum(amount) FROM payment WHERE invoice_no = :id AND pay_type = 'Credit_Note' ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $credit_note_amount = $row['sum(amount)'];
            }

            $payment_amount = $payment_amount - ($credit_amount + $credit_note_amount);

            // //check sales details
            if ($sales_id) {
                //Over payment
                if ($payment_amount > $sales_amount) {
                    $over_amount = $payment_amount - $sales_amount;

                    $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,action,sales_id,customer_id,loading_id,pay_type,credit_balance,app_id,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $ql = $db->prepare($sql);
                    $ql->execute(array($invoice, 0, $over_amount, 'Credit_Note', $date, $time, 3, $sales_id, $cus, $load, 'Credit_Note', $over_amount, $app_id, 'customer_credit_note'));
                }
            }
        }

        // get sales  data
        $result = $db->prepare("SELECT * FROM payment WHERE invoice_no=:id ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['transaction_id'];
            $ap_id = $row['app_id'];
            $invo = $row['invoice_no'];
        }

        // create success respond 
        $res = array(
            "cloud_id" => $id,
            "app_id" => $ap_id,
            "invoice_no" => $invo,
            "status" => "success",
            "message" => "",
        );

        array_push($result_array, $res);

        // Create log
        // $content = "cloud_id: " . $id . ", app_id: " . $ap_id . ", invoice: " . $invo . ", status: success, message: - , Date: " . date('Y-m-d') . ", Time: " . date('H:s:i');
        // log_init('payment', $content);
    } catch (PDOException $e) {

        // create error respond 
        $res = array(
            "cloud_id" => 0,
            "app_id" => 0,
            "invoice_no" => "",
            "status" => "failed",
            "message" => $e->getMessage(),
        );

        array_push($result_array, $res);

        // Create log
        $content = "cloud_id: 0, app_id: " . $app_id . ", invoice: " . $invoice . ", status: failed, message: " . $e->getMessage() . ", Date: " . date('Y-m-d') . ", Time: " . date('H:s:i');
        log_init('payment', $content);


        // Get the database name
        $stmt = $db->query("SELECT DATABASE()");
        $dbName = $stmt->fetchColumn();

        // Attempt to extract table name from error message
        $errorMessage = $e->getMessage();
        $tableName = null;

        // Example of extracting the table name using a regular expression
        if (preg_match('/(table|relation) "(\w+)"/i', $errorMessage, $matches)) {
            $tableName = $matches[2];
        }

        $fileName = "set_payment.php";

        // create message
        $message = "Please check error log..!    ( File: " . $e->getFile() . " On line: " . $e->getLine() . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $tableName . " )  ( Database Name: "  . $dbName . " )";

        // create discord alert
        discord($message);
    }
}

// send respond
echo (json_encode($result_array));
