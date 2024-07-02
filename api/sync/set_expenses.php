<?php
include('../../connect.php');
include('../../config.php');
include('log.php');
date_default_timezone_set("Asia/Colombo");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$expenses = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($expenses as $list) {

    $invoice = $list['invoice_no'];
    $load_id = $list['loading_id'];
    $comment = $list['comment'];
    $amount = $list['amount'];
    $sub_id = $list['type_id'];
    $date = $list['date'];
    $driver = $list['driver_id'];
    $app_id = $list['id'];
    $time = $list['time'];

    $pay_type = 'cash';
    $acc = 0;
    $lorry = 0;
    $acc_name = '';
    $lorry_no = '';
    $type_name = '';
    $sub_name = '';
    $driver_name = '';


    try {

        $type = 2;
        $re = $db->prepare("SELECT * FROM expenses_types WHERE sn=:id ");
        $re->bindParam(':id', $type);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $type_name = $r['type_name'];
        }

        $re = $db->prepare("SELECT * FROM expenses_sub_type WHERE id=:id ");
        $re->bindParam(':id', $sub_id);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $sub_name = $r['name'];
        }

        $result = $db->prepare("SELECT * FROM employee WHERE id=:id  ");
        $result->bindParam(':id', $driver);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $driver_name = $row['name'];
        }

        $result = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id ");
        $result->bindParam(':id', $load_id);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $lorry = $row['lorry_id'];
            $lorry_no = $row['lorry_no'];
        }

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no = '$invoice' AND app_id = '$app_id' AND sub_type = '$sub_id' AND loading_id = '$load_id' ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['id'];
        }

        if ($con == 0) {

            $sql = "INSERT INTO expenses_records (date,type_id,type,invoice_no,comment,amount,user,emp_id,loading_id,pay_type,sub_type,sub_type_name,lorry_id,lorry_no,credit_balance,paycose,action,app_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($date, $type, $type_name, $invoice, $comment, $amount, $driver, $driver, $load_id,  'credit', $sub_id, $sub_name, $lorry, $lorry_no, $amount, 'expenses', 1, $app_id));

            $result = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no=:id AND action = 1 ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $cr_id = $row['id'];
            }

            $sql = "UPDATE  expenses_records SET pay_amount = pay_amount + ?, credit_balance = credit_balance - ?, close_date = ? WHERE id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($amount, $amount, $date, $cr_id));

            $sql = "INSERT INTO expenses_records (date,type_id,type,invoice_no,pay_amount,amount,user,emp_id,loading_id,pay_type,sub_type,sub_type_name,lorry_id,lorry_no,credit_id,paycose,close_date,app_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($date, $type, $type_name, $invoice, $amount, $amount, $driver, $driver, $load_id, 'lorry_collection', $sub_id, $sub_name, $lorry, $lorry_no, $cr_id, 'payment', $date, $app_id));

            if ($sub_id == 4) {

                $now = date('Y-m-d');
                $emp_id = $list['employee_id'];

                $result = $db->prepare("SELECT * FROM employee WHERE id=:id ");
                $result->bindParam(':id', $emp_id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $name = $row['name'];
                }

                if ($emp_id) {
                    $sql = "INSERT INTO salary_advance (emp_id,name,amount,date,now,note) VALUES (?,?,?,?,?,?)";
                    $q = $db->prepare($sql);
                    $q->execute(array($emp_id, $name, $amount, $date, $now, $comment));
                }
            }
        }

        // get sales  data
        $result = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no='$invoice' AND sub_type = '$sub_id' AND loading_id = '$load_id' ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['id'];
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
        // log_init('expenses', $content);
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
        log_init('expenses', $content);


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

        $fileName = "set_expenses.php";

        // create message
        $message = "Please check error log..!    ( File: " . $e->getFile() . " On line: " . $e->getLine() . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $tableName . " )  ( Database Name: "  . $dbName . " )";

        // create discord alert
        discord($message);
    }
}

// send respond
echo (json_encode($result_array));
