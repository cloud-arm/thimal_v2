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
$damage = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($damage as $list) {

    $app_id = $list['id'];
    $load_id = $list['loading_id'];
    $complain_no = $list['complain_no'];
    $customer = $list['customer_id'];
    $cylinder_no = $list['cylinder_no'];
    $product = $list['product_id'];
    $reason = $list['reason_id'];
    $gas_weight = $list['gas_weight'];
    $comment = $list['comment'];
    $date = $list['date'];
    $invoice = $list['invoice_no'];
    $repl = $list['replacement'];

    $type = 'damage';
    $action = "register";


    try {

        if ($customer == 0) {

            $customer_name = 'Narangoda Group';
        } else {
            $result = $db->prepare("SELECT * FROM customer WHERE customer_id = :id ");
            $result->bindParam(':id', $customer);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $customer_name = $row['customer_name'];
            }
        }

        $result = $db->prepare("SELECT * FROM products WHERE product_id = :id ");
        $result->bindParam(':id', $product);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $product_name = $row['gen_name'];
        }

        $result = $db->prepare("SELECT * FROM loading WHERE transaction_id =:id ");
        $result->bindParam(':id', $load_id);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $lorry = $row['lorry_id'];
            $lorry_no = $row['lorry_no'];
            $user_id = $row['driver'];
            $user_name = $row['rep'];
        }

        $result = $db->prepare("SELECT * FROM damage_reason WHERE id = :id ");
        $result->bindParam(':id', $reason);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $reason_name = $row['name'];
        }


        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM damage WHERE invoice_no = '$invoice' AND app_id = '$app_id'  AND loading_id = '$load_id' ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['sn'];
        }

        if ($con == 0) {

            $sql = "INSERT INTO damage (complain_no,customer_id,customer_name,product_id,cylinder_no,cylinder_type,reason_id,reason,date,action,gas_weight,comment,type,location,invoice_no,position,loading_id,lorry_id,lorry_no,app_id,user_id,user_name,replacement) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($complain_no, $customer, $customer_name, $product, $cylinder_no, $product_name, $reason, $reason_name, $date, $action, $gas_weight, $comment, $type, 'Lorry', $invoice, 1, $load_id, $lorry, $lorry_no, $app_id, $user_id, $user_name, $repl));

            $sql = "UPDATE products  SET damage = damage + ? WHERE product_id = ?";
            $q = $db->prepare($sql);
            $q->execute(array(1, $product));

            if ($repl == 1) {
                $sql = "UPDATE loading_list  SET qty_sold = qty_sold - ?, damage = damage + ? WHERE product_code = ? AND loading_id = ? ";
                $q = $db->prepare($sql);
                $q->execute(array(1, 1, $product, $load_id));
            }

            $sql = "INSERT INTO damage_order (complain_no,date,action,type,location) VALUES (?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($complain_no, $date, $action, $type, 'Lorry'));
        }

        // get sales  data
        $result = $db->prepare("SELECT * FROM damage WHERE invoice_no='$invoice' AND loading_id = '$load_id' ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['sn'];
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
        // log_init('damage', $content);
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
        log_init('damage', $content);


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

        $fileName = "set_damage.php";

        // create message
        $message = "Please check error log..!    ( File: " . $e->getFile() . " On line: " . $e->getLine() . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $tableName . " )  ( Database Name: "  . $dbName . " )";

        // create discord alert
        discord($message);
    }
}

// send respond
echo (json_encode($result_array));
