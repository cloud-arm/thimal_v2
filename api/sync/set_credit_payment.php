<?php
include('../../connect.php');
include('../../log/log.php');
include("../../config.php");
date_default_timezone_set("Asia/Colombo");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$credit_payment = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($credit_payment as $list) {

    // get values
    $invoice = $list['invoice_no'];
    $pay_invoice = $list['pay_invoice'];
    $pay_amount = $list['pay_amount'];
    $credit_amount = $list['credit_amount'];
    $cus = $list['cus_id'];
    $app_id = $list['id'];


    $pay_id = 0; //
    $collection = 0; //
    $sales_id = 0; //
    $tr_id = 0; //


    //------------------------------------------------------------------//
    try {

        // get customer details
        $result = $db->prepare("SELECT * FROM customer WHERE customer_id=:id  ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $cus_name = $row['customer_name'];
        }

        // get collection details
        $result = $db->prepare("SELECT * FROM collection WHERE invoice_no=:id  ");
        $result->bindParam(':id', $pay_invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $collection = $row['id'];
            $load = $row['loading_id'];
            $date = $row['date'];
        }

        // get credit details
        $result = $db->prepare("SELECT * FROM payment WHERE invoice_no=:id AND pay_type = 'credit' ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $tr_id = $row['transaction_id'];
            $sales_id = $row['sales_id'];
        }

        // get bulk details
        $pay_id = 0;
        $pay_type = '';
        $result = $db->prepare("SELECT * FROM payment WHERE invoice_no=:id  ");
        $result->bindParam(':id', $pay_invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $pay_id = $row['transaction_id'];
            $pay_type = $row['pay_type'];
        }

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM credit_payment WHERE pay_invoice = '$pay_invoice' AND app_id = '$app_id' AND loading_id = '$load' ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['id'];
        }

        if ($con == 0) {

            $insertData = array(
                "data" => array(
                    "invoice_no" => $invoice,
                    "pay_amount" => $pay_amount,
                    "credit_amount" => $credit_amount,
                    "type" => $pay_type,
                    "date" => $date,
                    "cus_id" => $cus,
                    "cus" => $cus_name,
                    "action" => 2,
                    "loading_id" => $load,
                    "pay_id" => $pay_id,
                    "collection_id" => $collection,
                    "sales_id" => $sales_id,
                    "tr_id" => $tr_id,
                    "app_id" => $app_id,
                    "pay_invoice" => $pay_invoice,
                ),
                "other" => array(
                    "data_id" => $invoice,
                    "data_name" => "invoice",
                ),
            );

            $status = insert("credit_payment", $insertData, '../../', 'set_credit_payment.php');
        } else {
            $status = array(
                "status" => "success",
                "message" => "Already included",
            );
        }

        // get sales  data
        $id = 0;
        $ap_id = 0;
        $invo = '';
        $result = $db->prepare("SELECT * FROM credit_payment WHERE invoice_no=:id ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['id'];
            $ap_id = $row['app_id'];
            $invo = $row['invoice_no'];
        }

        // create success respond 
        $result_array[] = array(
            "cloud_id" => $id,
            "app_id" => $ap_id,
            "invoice_no" => $invo,
            "status" => $status['status'],
            "message" => $status['message'],
        );
    } catch (PDOException $e) {

        // create error respond 
        $result_array[] = array(
            "cloud_id" => 0,
            "app_id" => 0,
            "invoice_no" => "",
            "status" => "failed",
            "message" => $e->getMessage(),
        );

        // Create log
        $content = "cloud_id: 0, app_id: " . $app_id . ", invoice: " . $invoice . ", status: failed, message: " . $e->getMessage() . ", Date: " . date('Y-m-d') . ", Time: " . date('H:s:i');
        log_init('credit_payment', $content, 'txt', '../../');


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

        $fileName = "set_sales.php";

        // create message
        $message = "Please check error log..!    ( File: " . $e->getFile() . " On line: " . $e->getLine() . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $tableName . " )  ( Database Name: "  . $dbName . " )";

        // create discord alert
        discord($message);
    }
}

// send respond
echo (json_encode($result_array));
