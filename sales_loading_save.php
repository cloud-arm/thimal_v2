<?php
session_start();
include('config.php');
include('sub_class/customer_transaction.php');
include('sub_class/vat_transaction.php');
date_default_timezone_set("Asia/Colombo");


$now = date("Y-m-d");
$time = date('H:i:s');

$driver = $_SESSION['SESS_MEMBER_ID'];
$driver_name = $_SESSION['SESS_FIRST_NAME'];

$invoice = $_POST['id'];
$cus = $_POST['customer'];
$pay_type = $_POST['pay_type'];
$note = $_POST['note'];
$old_sales_id = $_POST['sales_id'];


$chq_no = '';
$chq_date = '';

$payment = 0;


//checking list item
$con = 0;
$result = select("sales_list", "id", "invoice_no = '" . $invoice . "'");
for ($i = 0; $row = $result->fetch(); $i++) {
    $con = $row['id'];
}

if ($con) {

    // old sales details
    $result = select("sales", "*", "transaction_id = '" . $old_sales_id . "'");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $old_invo = $row['invoice_number'];
        $old_amount = $row['amount'];
        $old_cus = $row['customer_id'];
        $load = $row['loading_id'];
        $date = $row['date'];
        $time = $row['time'];
        $root = $row['root'];
    }

    // loading details
    $result = select("loading", "*", "transaction_id = '" . $load . "'");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $lorry_id = $row['lorry_id'];
        $lorry = $row['lorry_no'];
    }

    //get customer
    $result = select("customer", "*", "customer_id = '" . $cus . "'");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $cus_name = $row['customer_name'];
        $address = $row['address'];
        $vat_no = $row['vat_no'];
    }

    //checking duplicate
    $con = 0;
    $result = select("sales", "transaction_id", "invoice_number = '" . $invoice . "'");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $con = $row['transaction_id'];
    }

    if ($con == 0) {


        // inventory records -----------------------------
        $result = select("sales_list", "*", "invoice_no = '" . $invoice . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $list = $row['id'];
            $id = $row['product_id'];
            $name = $row['name'];
            $value = $row['value'];
            $qty = $row['qty'];
            $temp_qty = $row['qty'];

            do {
                if (isset($id)) {
                } else {
                    $id = 0;
                }
                $qty_blc = 0;
                $temp_sell = 0;
                $temp_cost = 0;
                $st_id = 0;

                $re = select_query("SELECT * FROM stock WHERE product_id='$id' AND qty_balance>0  ORDER BY id ASC LIMIT 1 ");
                for ($k = 0; $r = $re->fetch(); $k++) {
                    $st_qty = $r['qty_balance'];
                    $st_id = $r['id'];
                    $temp_sell = $r['sell'];
                    $temp_cost = $r['cost'];
                    $cost = $r['cost'];

                    if ($st_qty < $temp_qty) {

                        $temp_qty = $temp_qty - $st_qty;

                        // update stock qty
                        echo 'Line: 107 ' . query("UPDATE stock  SET qty_balance = 0 WHERE id = '" . $st_id . "' ");

                        // set inventory record
                        $insertData = array(
                            "data" => array(
                                "invoice_no" => $invoice,
                                "product_id" => $id,
                                "name" => $name,
                                "type" => "out",
                                "balance" => 0,
                                "qty" => $st_qty,
                                "date" => $now,
                                "sell" => $temp_sell,
                                "cost" => $temp_cost * $temp_qty,
                                "stock_id" => $st_id,
                            ),
                            "other" => array(
                                "data_id" => $st_id,
                                "data_name" => "inventory",
                            ),
                        );
                        $status = insert("inventory", $insertData);
                    } else {

                        $qty_blc = $st_qty - $temp_qty;

                        // update stock qty
                        echo 'Line: 134 ' . query("UPDATE stock  SET qty_balance = qty_balance - '" . $temp_qty . "' WHERE id = '" . $st_id . "' ");

                        // set inventory record
                        $insertData = array(
                            "data" => array(
                                "invoice_no" => $invoice,
                                "product_id" => $id,
                                "name" => $name,
                                "type" => "out",
                                "balance" => $qty_blc,
                                "qty" => $temp_qty,
                                "date" => $now,
                                "sell" => $temp_sell,
                                "cost" => $temp_cost * $temp_qty,
                                "stock_id" => $st_id,
                            ),
                            "other" => array(
                                "data_id" => $st_id,
                                "data_name" => "inventory",
                            ),
                        );
                        $status = insert("inventory", $insertData);

                        $temp_qty = 0;
                    }
                }
                if ($st_id == 0) {
                    $temp_qty = 0;
                }
            } while ($temp_qty > 0);

            // update product qty
            echo 'Line: 166 ' . query("UPDATE products  SET qty = qty - '$qty' WHERE product_id = '$id' ");

            // update loading list qty
            echo 'Line: 169 ' . query("UPDATE loading_list  SET qty_sold = qty_sold - '$qty', unload_qty = unload_qty - '$qty' WHERE product_code = '$id' AND loading_id = '$load' ");


            // cost amount
            $cost_amount = 0;
            $res = select("inventory", "sum(cost)", "invoice_no = '" . $invoice . "' AND product_id = '" . $id . "' ");
            for ($f = 0; $ro = $res->fetch(); $f++) {
                $cost_amount = $ro['sum(cost)'];
            }

            $wv_cost = ($cost_amount / 118) * 100;
            // profit
            $profit = $value - $wv_cost;
            $profit = number_format($profit, 2, ".", "");

            // update sales_list
            $updateData = array(
                "profit" => $profit,
                "cost_amount" => $cost_amount,
                "cus_id" => $cus,
                "action" => 0,
            );
            echo 'Line: 191 ' . update("sales_list", $updateData, "id = " . $list);
        }


        // get sales section
        $result = select_query("SELECT sum(cost_amount),sum(profit),sum(vat),sum(value),sum(amount),sum(dic) FROM sales_list WHERE invoice_no= '$invoice'  ");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $cost = $row['sum(cost_amount)'];
            $profit = $row['sum(profit)'];
            $vat = $row['sum(vat)'];
            $value = $row['sum(value)'];
            $amount = $row['sum(amount)'];
            $discount = $row['sum(dic)'];
        }

        $balance = $amount - $payment;

        $term = 0;
        if ($vat_no == '') {
            $vat_action = 0;
        } else {
            $vat_action = 1;
        }

        $now_date = $now;
        if ($date == $now) {
            $now_date = '';
        }

        // insert sales
        $insertData = array(
            "data" => array(
                "invoice_number" => $invoice,
                "cashier" => $driver,
                "date" => $date,
                "time" => $time,
                "amount" => $amount,
                "balance" => $balance,
                "discount" => $discount,
                "cost" => $cost,
                "profit" => $profit,
                "name" => $cus_name,
                "root" => $root,
                "rep" => $driver_name,
                "lorry_no" => $lorry,
                "term" => $term,
                "loading_id" => $load,
                "customer_id" => $cus,
                "action" => 1,
                "address" => $address,
                "vat" => $vat,
                "value" => $value,
                "cus_vat_no" => $vat_no,
                "vat_action" => $vat_action,
                "type" => $pay_type,
                "now" => $now_date,
                "reason" => $note,
            ),
            "other" => array(
                "data_id" => $invoice,
                "data_name" => "sales",
            ),
        );
        $status = insert("sales", $insertData);

        // get sales
        $sales_id = 0;
        $result = select("sales", "transaction_id", "invoice_number = '" . $invoice . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $sales_id = $row['transaction_id'];
        }

        //customer transaction
        $data = array(
            "invoice" => $invoice,
            "type" => 'debit',
            "date" => $now,
            "time" => $time,
            "amount" => $amount,
            "sales" => $sales_id,
            "customer" => $cus,
            "userid" => $driver,
            "username" => $driver_name,
        );
        customer_transaction($data);

        if ($discount > 0) {

            // insert reimbursement
            $insertData = array(
                "data" => array(
                    "invoice_no" => $invoice,
                    "type" => "active",
                    "date" => $date,
                    "time" => $time,
                    "amount" => $discount,
                    "balance" => $discount,
                    "pay_type" => "credit",
                    "lorry_no" => $lorry,
                    "lorry_id" => $lorry_id,
                    "loading_id" => $load,
                    "customer_id" => $cus,
                    "customer_name" => $cus_name,
                ),
                "other" => array(
                    "data_id" => $invoice,
                    "data_name" => "reimbursement",
                ),
            );
            $status = insert("reimbursement", $insertData);
        }

        $vat_id = 1;

        //vat transaction
        $data = array(
            "invoice" => $invoice,
            "type" => 'Credit',
            "date" => $date,
            "time" => $time,
            "record_type" => "invoice",
            "vat_id" => $vat_id,
            "vat" => $vat,
            "value" => $value,
            "vat_no" => $vat_no,
            "userid" => $driver,
            "username" => $driver_name,
        );
        vat_transaction($data);

        // $sales_id = 0;
        $paycose = 'invoice_payment';
        $pay_amount = $payment;
        $credit = $balance;

        // insert payment
        $insertData = array(
            "data" => array(
                "invoice_no" => $invoice,
                "pay_amount" => 0,
                "date" => $date,
                "time" => $time,
                "amount" => $amount,
                "type" => $pay_type,
                "pay_type" => $pay_type,
                "action" => 2,
                "sales_id" => $sales_id,
                "credit_balance" => $credit,
                "customer_id" => $cus,
                "paycose" => $paycose,
                "loading_id" => $load,
                "memo" => $note,
                "user_id" => $driver,
            ),
            "other" => array(
                "data_id" => $invoice,
                "data_name" => "payment",
            ),
        );
        $status = insert("payment", $insertData);

        // Old sales disabled
        $result = select("sales_list", "*", "invoice_no = '" . $old_invo . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $qty = $row['qty'];
            $cod = $row['product_id'];
            $loading_id = $row['loading_id'];

            //update loading list qty
            echo 'Line: 360 ' . query("UPDATE loading_list  SET qty_sold = qty_sold + '$qty', unload_qty = unload_qty + '$qty' WHERE product_code = '$cod' AND loading_id = '$loading_id' ");

            // update product qty
            echo 'Line: 363 ' . query("UPDATE products  SET qty = qty + '$qty' WHERE product_id = '$cod' ");
        }

        // update inventory
        $result = select("inventory", "*", "invoice_no = '" . $old_invo . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['product_id'];
            $name = $row['name'];
            $qty = $row['qty'];
            $sell = $row['sell'];
            $cost = $row['cost'];
            $qty_blc = $row['balance'];
            $st_id = $row['stock_id'];

            $st_qty = 0;
            $re = select("stock", "qty_balance", "id = '" . $st_id . "'");
            for ($k = 0; $r = $re->fetch(); $k++) {
                $st_qty = $r['qty_balance'];
            }

            // update stock qty
            echo 'Line: 384 ' . query("UPDATE stock  SET qty_balance = qty_balance + '" . $qty . "' WHERE id = '" . $st_id . "' ");

            // set inventory record
            $insertData = array(
                "data" => array(
                    "invoice_no" => $old_invo,
                    "product_id" => $id,
                    "name" => $name,
                    "type" => "in",
                    "balance" => $st_qty + $qty,
                    "qty" => $qty,
                    "date" => $now,
                    "sell" => $sell,
                    "cost" => $cost,
                    "stock_id" => $st_id,
                ),
                "other" => array(
                    "data_id" => $st_id,
                    "data_name" => "inventory",
                ),
            );
            $status = insert("inventory", $insertData);
        }

        // update old sales
        $updateData = array(
            "action" => "5",
        );
        echo 'Line: 412 ' . update("sales", $updateData, "invoice_number = '" . $old_invo . "'");

        // update old payment
        $updateData = array(
            "action" => "0",
            "dll" => "1",
        );
        echo 'Line: 419 ' . update("payment", $updateData, "invoice_no = '" . $old_invo . "'");

        // update old sales list
        $updateData = array(
            "action" => "1",
        );
        echo 'Line: 425 ' . update("sales_list", $updateData, "invoice_no = '" . $old_invo . "'");

        //customer transaction
        $data = array(
            "invoice" => $old_invo,
            "type" => 'credit',
            "date" => $date,
            "time" => $time,
            "amount" => $old_amount,
            "sales" => $old_sales_id,
            "customer" => $old_cus,
            "userid" => $driver,
            "username" => $driver_name,
        );
        customer_transaction($data);

        $result = select("sales", "*", "invoice_number = '" . $old_invo . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $vat = $row['vat'];
            $value = $row['value'];
            $vat_no = $row['cus_vat_no'];
            $pay_type = $row['type'];
        }

        $vat_id = 1;

        //vat transaction
        $data = array(
            "invoice" => $old_invo,
            "type" => 'Debit',
            "date" => $date,
            "time" => $time,
            "record_type" => "invoice_delete",
            "vat_id" => $vat_id,
            "vat" => $vat,
            "value" => $value,
            "vat_no" => $vat_no,
            "userid" => $driver,
            "username" => $driver_name,
        );
        vat_transaction($data);

        // old payment details
        $pay_type = '';
        $result = select("payment", "*", "pay_type = 'cash' AND invoice_no = '" . $old_invo . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $pay_type = $row['pay_type'];
            $amount = $row['amount'];
            $cus = $row['customer_id'];
            $load = $row['loading_id'];
        }

        if ($pay_type == 'cash' || $pay_type == 'Cash') {
            // insert payment
            $insertData = array(
                "data" => array(
                    "invoice_no" => $old_invo,
                    "pay_amount" => 0,
                    "date" => $now,
                    "time" => $time,
                    "amount" => $amount,
                    "type" => "Credit_Note",
                    "pay_type" => "Credit_Note",
                    "action" => 3,
                    "sales_id" => $old_sales_id,
                    "credit_balance" => $amount,
                    "customer_id" => $cus,
                    "paycose" => "customer_credit_note",
                    "loading_id" => $load,
                    "memo" => $note,
                    "user_id" => $driver,
                ),
                "other" => array(
                    "data_id" => $old_invo,
                    "data_name" => "payment",
                ),
            );
            $status = insert("payment", $insertData);

            //customer transaction
            $data = array(
                "invoice" => $old_invo,
                "type" => 'credit',
                "date" => $now,
                "time" => $time,
                "amount" => $amount,
                "sales" => $old_sales_id,
                "customer" => $cus,
                "userid" => $driver,
                "username" => $driver_name,
            );
            customer_transaction($data);
        }

        $pay_type = '';
        $result = select("payment", "*", "pay_type = 'chq' AND chq_action > 0 AND invoice_no = '" . $old_invo . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $pay_type = $row['pay_type'];
            $amount = $row['amount'];
            $cus = $row['customer_id'];
            $load = $row['loading_id'];
        }

        if ($pay_type == 'chq' || $pay_type == 'Chq') {
            // insert payment
            $insertData = array(
                "data" => array(
                    "invoice_no" => $old_invo,
                    "pay_amount" => 0,
                    "date" => $now,
                    "time" => $time,
                    "amount" => $amount,
                    "type" => "Credit_Note",
                    "pay_type" => "Credit_Note",
                    "action" => 3,
                    "sales_id" => $old_sales_id,
                    "credit_balance" => $amount,
                    "customer_id" => $cus,
                    "paycose" => "customer_credit_note",
                    "loading_id" => $load,
                    "memo" => $note,
                    "user_id" => $driver,
                ),
                "other" => array(
                    "data_id" => $old_invo,
                    "data_name" => "payment",
                ),
            );
            $status = insert("payment", $insertData);

            //customer transaction
            $data = array(
                "invoice" => $old_invo,
                "type" => 'credit',
                "date" => $now,
                "time" => $time,
                "amount" => $amount,
                "sales" => $old_sales_id,
                "customer" => $cus,
                "userid" => $driver,
                "username" => $driver_name,
            );
            customer_transaction($data);
        }

        $pay_type = '';
        $result = select("payment", "*", "pay_type = 'credit_payment' AND invoice_no = '" . $old_invo . "'");
        for ($i = 0; $row = $result->fetch(); $i++) {
            $pay_type = $row['pay_type'];
            $amount = $row['amount'];
            $cus = $row['customer_id'];
            $load = $row['loading_id'];
        }

        if ($pay_type == 'credit_payment') {
            // insert payment
            $insertData = array(
                "data" => array(
                    "invoice_no" => $old_invo,
                    "pay_amount" => 0,
                    "date" => $now,
                    "time" => $time,
                    "amount" => $amount,
                    "type" => "Credit_Note",
                    "pay_type" => "Credit_Note",
                    "action" => 3,
                    "sales_id" => $old_sales_id,
                    "credit_balance" => $amount,
                    "customer_id" => $cus,
                    "paycose" => "customer_credit_note",
                    "loading_id" => $load,
                    "memo" => $note,
                    "user_id" => $driver,
                ),
                "other" => array(
                    "data_id" => $old_invo,
                    "data_name" => "payment",
                ),
            );
            $status = insert("payment", $insertData);

            //customer transaction
            $data = array(
                "invoice" => $old_invo,
                "type" => 'credit',
                "date" => $now,
                "time" => $time,
                "amount" => $amount,
                "sales" => $old_sales_id,
                "customer" => $cus,
                "userid" => $driver,
                "username" => $driver_name,
            );
            customer_transaction($data);
        }
    }

    $invo = base64_encode($invoice);
    header("location: bill2.php?invo=$invo");
} else {

    $err = 4;

    $invo = base64_encode($invoice);
    $sid = base64_encode($old_sales_id);
    header("location: sales_loading_sales.php?sid=$sid&id=$invo&err=$err");
}
