<?php
include('../../connect.php');
include("../../config.php");
include('log.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get json data
$json_data = file_get_contents('php://input');

// get values
$sales_list = json_decode($json_data, true);

// respond init
$result_array = array();

foreach ($sales_list as $list) {

    $invoice = $list['invoice_no'];
    $pid = $list['product_id'];
    $qty = $list['qty'];
    $price = $list['price'];
    $discount = $list['discount'];
    $load = $list['loading_id'];
    $date = $list['date'];
    $cus = $list['cus_id'];
    $price_id = $list['price_id'];
    $app_id = $list['id'];

    //get lorry
    $result = $db->prepare("SELECT * FROM loading WHERE transaction_id = :id  ");
    $result->bindParam(':id', $load);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $lorry = $row['lorry_no'];
    }

    //get product
    $result = $db->prepare("SELECT * FROM products WHERE product_id = :id  ");
    $result->bindParam(':id', $pid);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $name = $row['gen_name'];
        $comm = $row['commission'];
    }

    //checking stock qty
    $con_qty = 0;
    $result = $db->prepare("SELECT sum(qty_balance) FROM stock WHERE product_id = :id  ");
    $result->bindParam(':id', $pid);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $con_qty = $row['sum(qty_balance)'];
    }

    if ($con_qty >= $qty) {

        //------------------------------------------------------------------------------//
        try {

            //checking duplicate
            $con = 0;
            $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = '$invoice' AND app_id = '$app_id' AND loading_id = '$load' ");
            $result->bindParam(':id', $cus);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $con = $row['id'];
            }

            if ($con == 0) {

                $cost_amount = 0;
                // inventory records -----

                $id = $pid;

                $temp_qty = $qty;

                do {
                    if (isset($id)) {
                    } else {
                        $id = 0;
                    }
                    $qty_blc = 0;
                    $temp_sell = 0;
                    $temp_cost = 0;
                    $st_id = 0;
                    $re = $db->prepare("SELECT * FROM stock WHERE product_id=:id AND qty_balance>0  ORDER BY id ASC LIMIT 1 ");
                    $re->bindParam(':id', $id);
                    $re->execute();
                    for ($k = 0; $r = $re->fetch(); $k++) {
                        $st_qty = $r['qty_balance'];
                        $st_id = $r['id'];
                        $temp_sell = $r['sell'];
                        $temp_cost = $r['cost'];

                        if ($st_qty < $temp_qty) {

                            $temp_qty = $temp_qty - $st_qty;

                            // update stock qty
                            $sql = "UPDATE stock SET  qty_balance=? WHERE id=?";
                            $ql = $db->prepare($sql);
                            $ql->execute(array(0, $st_id));

                            // set inventory record
                            $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,sell,cost,stock_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                            $ql = $db->prepare($sql);
                            $ql->execute(array($id, $name, $invoice, 'out', 0, $st_qty, $date, $temp_sell, $temp_cost * $temp_qty, $st_id));
                        } else {

                            $qty_blc = $st_qty - $temp_qty;

                            // update stock qty
                            $sql = "UPDATE stock SET qty_balance=? WHERE id=?";
                            $ql = $db->prepare($sql);
                            $ql->execute(array($qty_blc, $st_id));

                            // set inventory record
                            $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,sell,cost,stock_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                            $ql = $db->prepare($sql);
                            $ql->execute(array($id, $name, $invoice, 'out', $qty_blc, $temp_qty, $date, $temp_sell, $temp_cost * $temp_qty, $st_id));

                            $temp_qty = 0;
                        }
                    }
                    if ($st_id == 0) {
                        $temp_qty = 0;
                    }
                } while ($temp_qty > 0);

                // update loading qty
                $sql = "UPDATE loading_list SET qty_sold = qty_sold-? WHERE loading_id = ? AND product_code = ?";
                $ql = $db->prepare($sql);
                $ql->execute(array($qty, $load, $pid));
            }
            // ------------

            // get cost amount
            $result = $db->prepare("SELECT sum(cost) FROM inventory WHERE invoice_no=:id AND product_id='$pid' ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $cost_amount = $row['sum(cost)'];
            }

            // amount
            $amount = $price * $qty;

            // vat value
            $vat = ($amount / 118) * 18;

            // without vat amount
            $value = ($amount / 118) * 100;

            // without vat cost
            $wv_cost = ($cost_amount / 118) * 100;

            // profit
            $profit = $value - $wv_cost;
            $profit = number_format($profit, 2, ".", "");

            // commission
            $commission = $comm * $qty;


            //checking duplicate
            $con = 0;
            $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = '$invoice' AND app_id = '$app_id' AND loading_id = '$load' ");
            $result->bindParam(':id', $cus);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $con = $row['id'];
            }

            if ($con == 0) {

                // insert query
                $sql = "INSERT INTO sales_list (invoice_no,product_id,name,amount,cost_amount,dic,qty,price,profit,date,loading_id,action,cus_id,price_id,vat,value,app_id,commission) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $ql = $db->prepare($sql);
                $ql->execute(array($invoice, $pid, $name, $amount, $cost_amount, $discount, $qty, $price, $profit, $date, $load, 0, $cus, $price_id, $vat, $value, $app_id, $commission));
            }

            // get sales list id
            $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no=:id AND product_id = $pid ");
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
            // log_init('sales_list', $content);
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
            log_init('sales_list', $content);



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

            $fileName = "set_sales_list.php";

            // create message
            $message = "Please check error log..!    ( File: " . $e->getFile() . " On line: " . $e->getLine() . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $tableName . " )  ( Database Name: "  . $dbName . " )";

            // create discord alert
            discord($message);
        }
    } else {

        // create error respond 
        $res = array(
            "cloud_id" => 0,
            "app_id" => 0,
            "invoice_no" => "",
            "status" => "failed",
            "message" => "The stock qty balance is insufficient.",
        );

        array_push($result_array, $res);

        // Create log
        $content = "cloud_id: 0, app_id: " . $app_id . ", invoice: " . $invoice . ", Product: " . $pid . ", Qty: " . $qty . ", StQty: " . $con_qty . ", status: failed, message: The stock qty balance is insufficient., Date: " . date('Y-m-d') . ", Time: " . date('H:s:i');
        log_init('sales_list', $content);

        // whatsapp message
        $message = "The stock qty balance is insufficient..!  Lorry No: " . $lorry . ".   Invoice: " . $invoice . ".   Product: " . $name . ".   Stock qty: " . $con_qty . ".   Sales qty: " . $qty . ".";
        $url = '';

        // $contact = '94712010051';
        // whatsApp($contact, $message, $url);

        $contact = '94772955659';
        whatsApp($contact, $message, $url);

        // create discord alert
        discord($message);
    }
}

// send respond
echo (json_encode($result_array));
