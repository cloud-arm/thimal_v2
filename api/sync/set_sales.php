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
$sales = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($sales as $list) {

    // get values
    $load = $list['loading_id'];
    $cus = $list['cus_id'];
    $invoice = $list['invoice_no'];
    $amount = $list['amount'];
    $balance = $list['balance'];
    $discount = $list['discount'];
    $vat_action = $list['vat_action'];
    $vat_no = $list['vat_no'];
    $date = $list['date'];
    $time = $list['time'];
    $app_id = $list['id'];


    //------------------------------------------------------------------//
    try {

        // get loading details
        $result = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id AND action='load' ");
        $result->bindParam(':id', $load);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $lorry = $row['lorry_no'];
            $lorry_id = $row['lorry_id'];
            $root = $row['root'];
            $driver = $row['driver'];
            $term = $row['term'];
        }

        // get employee details
        $result = $db->prepare("SELECT * FROM employee WHERE id=:id  ");
        $result->bindParam(':id', $driver);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $driver_name = $row['name'];
        }

        $online_action = 0;
        // get customer details
        $result = $db->prepare("SELECT * FROM customer WHERE customer_id=:id  ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $cus_name = $row['customer_name'];
            $address = $row['address'];
            $online_action = $row['online_action'];
        }

        // get sales details
        $result = $db->prepare("SELECT sum(cost_amount),sum(profit),sum(vat),sum(value) FROM sales_list WHERE invoice_no=:id  ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $cost = $row['sum(cost_amount)'];
            $profit = $row['sum(profit)'];
            $vat = $row['sum(vat)'];
            $value = $row['sum(value)'];
        }

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = '$invoice' AND app_id = '$app_id' AND loading_id = '$load' ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['transaction_id'];
        }

        if ($con == 0) {

            // insert sales
            $sql = "INSERT INTO sales (invoice_number,cashier,date,time,amount,balance,discount,cost,profit,name,root,rep,lorry_no,term,loading_id,customer_id,action,address,vat,value,cus_vat_no,vat_action,app_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, $driver, $date, $time, $amount, $balance, $discount, $cost, $profit, $cus_name, $root, $driver_name, $lorry, $term, $load, $cus, 1, $address, $vat, $value, $vat_no, $vat_action, $app_id));

            if ($discount > 0) {

                // insert reimbursement
                $sql = "INSERT INTO reimbursement (invoice_no,type,date,time,amount,balance,pay_type,lorry_no,lorry_id,loading_id,customer_id,customer_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                $ql = $db->prepare($sql);
                $ql->execute(array($invoice, 'active', $date, $time, $discount, $discount, 'credit', $lorry, $lorry_id, $load, $cus, $cus_name));
            }

            $vat_id = 1;
            //update vat amount
            $sql = "UPDATE vat_account SET amount = amount + ? WHERE id = ?";
            $ql = $db->prepare($sql);
            $ql->execute(array($vat, $vat_id));

            $vat_acc = '';
            //get vat acc
            $result = $db->prepare("SELECT * FROM vat_account WHERE id = :id ");
            $result->bindParam(':id', $vat_id);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $vat_acc = $row['vat_no'];
            }

            // insert vat record
            $sql = "INSERT INTO vat_record (invoice_no,type,date,time,record_type,acc_id,acc_no,vat,value,vat_no,user_name,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, 'Credit', $date, $time, 'invoice', $vat_id, $vat_acc, $vat, $value, $vat_no, $driver_name, $driver));

            // get sales
            $sales_id = 0;
            $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $sales_id = $row['transaction_id'];
            }

            //update customer balance
            $sql = "UPDATE customer SET balance = balance - ? WHERE customer_id = ?";
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
            $sql = "INSERT INTO customer_record (invoice_no,type,date,time,debit,balance,sales_id,customer_id,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, 'debit', $date, $time, $amount, $cus_balance, $sales_id, $cus, $driver, $driver_name));
        }

        // //check sales details
        if ($online_action && check_sales($db, $invoice)) {
            send_invoice($db, $invoice);
        }
        //}

        // get sales  data
        $result = $db->prepare("SELECT * FROM sales WHERE invoice_number=:id ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['transaction_id'];
            $ap_id = $row['app_id'];
            $invo = $row['invoice_number'];
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
        // log_init('sales', $content);
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
        log_init('sales', $content);


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
