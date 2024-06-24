<?php
include('../../connect.php');
include('log.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$collection = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($collection as $list) {

    // get values
    $invoice = $list['pay_invoice'];
    $amount = $list['amount'];
    $pay_type = $list['pay_type'];
    $chq_no = $list['chq_no'];
    $chq_date = $list['chq_date'];
    $date = $list['date'];
    $user = $list['user_id'];
    $bank = $list['bank'];
    $load = $list['loading_id'];
    $app_id = $list['id'];

    $time = date('H:i:s');

    $cus = 0;//
    $cus_name = '';//

    // get customer details
    $result = $db->prepare("SELECT * FROM customer WHERE customer_id=:id  ");
    $result->bindParam(':id', $cus);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $cus_name = $row['customer_name'];
    }
    //------------------------------------------------------------------//
    try {

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM collection WHERE invoice_no = '$invoice' AND app_id = '$app_id' AND loading_id = '$load' ");
        $result->bindParam(':id', $cus);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['id'];
        }

        if ($con == 0) {

            $action = 11;

            if ($pay_type == "chq") {
                $pay_amount = 0;
                if ($chq_no == "" || $chq_date == "" || $bank == "") {
                    $error_id = 5;
                }
            }

            if ($pay_type == "cash") {
                $pay_amount = $amount;
            }

            $credit = 0;
            if ($amount > $pay_amount) {
                $credit = $amount - $pay_amount;
            }

            // insert collection
            $pay_id = 0;
            $sql = "INSERT INTO collection (invoice_no,amount,pay_type,type,date,chq_no,bank,chq_date,customer_id,customer,action,loading_id,pay_id,user_id,app_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, $amount, $pay_type, 0, $date,  $chq_no, $bank, $chq_date, $cus, $cus_name, 0, $load, $pay_id, $user, $app_id));

            // get collection id
            $id = 0;
            $result = $db->prepare("SELECT * FROM collection WHERE invoice_no=:id ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $id = $row['id'];
            }

            // insert bulk chq
            $sales_id = 0;
            $sql = "INSERT INTO payment (collection_id,pay_amount,amount,type,pay_type,date,chq_no,chq_date,chq_bank,sales_id,customer_id,pay_credit,action,loading_id,credit_balance,time,invoice_no,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($id, $pay_amount, $amount, $pay_type, $pay_type, $date, $chq_no, $chq_date, $bank, $sales_id, $cus, 1, $action, $load, $credit, $time, $invoice, 'credit_payment'));


            // get payment id
            $result = $db->prepare("SELECT * FROM payment WHERE collection_id=:id ");
            $result->bindParam(':id', $id);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $pay_id = $row['transaction_id'];
            }

            // update pay id
            $sql = "UPDATE collection SET pay_id=? WHERE id=?";
            $q = $db->prepare($sql);
            $q->execute(array($pay_id, $id));
        }

        // get sales  data
        $result = $db->prepare("SELECT * FROM collection WHERE invoice_no=:id ");
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
        // log_init('collection', $content);
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
        log_init('collection', $content);
    }
}

// send respond
echo (json_encode($result_array));
