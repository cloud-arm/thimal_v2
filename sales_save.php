<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$now = date("Y-m-d");
$time = date('H:i:s');

$driver = $_SESSION['SESS_MEMBER_ID'];
$driver_name = $_SESSION['SESS_FIRST_NAME'];

$invoice = $_POST['id'];
$cus = $_POST['customer'];
$pay_type = $_POST['pay_type'];
$payment = $_POST['amount'];
$date = $_POST['date'];

$chq_no = '';
$chq_date = '';
$chq_bank = '';
if ($pay_type == 'Chq') {
    $chq_no = $_POST['chq_no'];
    $chq_date = $_POST['chq_date'];
    $chq_bank = $_POST['chq_bank'];
}
if ($payment == 0) {
    $pay_type = 'Credit';
} else
if ($pay_type == 'Credit') {
    $payment = 0;
}


//checking list item
$con = 0;
$result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = :id  ");
$result->bindParam(':id', $invoice);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $con = $row['id'];
}

if ($con > 0) {

    //get customer
    $result = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
    $result->bindParam(':id', $cus);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $cus_name = $row['customer_name'];
        $address = $row['address'];
        $vat_no = $row['vat_no'];
    }

    //checking duplicate
    $con = 0;
    $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
    $result->bindParam(':id', $invoice);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $con = $row['transaction_id'];
    }

    if ($con == 0) {


        // inventory records -----------------------------
        $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = :id ");
        $result->bindParam(':id', $invoice);
        $result->execute();
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
                $re = $db->prepare("SELECT * FROM stock WHERE product_id=:id AND qty_balance>0  ORDER BY id ASC LIMIT 1 ");
                $re->bindParam(':id', $id);
                $re->execute();
                for ($k = 0; $r = $re->fetch(); $k++) {
                    $st_qty = $r['qty_balance'];
                    $st_id = $r['id'];
                    $temp_sell = $r['sell'];
                    $temp_cost = $r['cost'];
                    $cost = $r['cost'];

                    if ($st_qty < $temp_qty) {

                        $temp_qty = $temp_qty - $st_qty;

                        // update stock qty
                        $sql = "UPDATE stock SET  qty_balance=? WHERE id=?";
                        $ql = $db->prepare($sql);
                        $ql->execute(array(0, $st_id));

                        // set inventory record
                        $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,sell,cost,stock_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                        $ql = $db->prepare($sql);
                        $ql->execute(array($id, $name, $invoice, 'out', 0, $st_qty, $now, $temp_sell, $temp_cost * $temp_qty, $st_id));
                    } else {

                        $qty_blc = $st_qty - $temp_qty;

                        // update stock qty
                        $sql = "UPDATE stock SET qty_balance=? WHERE id=?";
                        $ql = $db->prepare($sql);
                        $ql->execute(array($qty_blc, $st_id));

                        // set inventory record
                        $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,sell,cost,stock_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                        $ql = $db->prepare($sql);
                        $ql->execute(array($id, $name, $invoice, 'out', $qty_blc, $temp_qty, $now, $temp_sell, $temp_cost * $temp_qty, $st_id));

                        $temp_qty = 0;
                    }
                }
                if ($st_id == 0) {
                    $temp_qty = 0;
                }
            } while ($temp_qty > 0);

            // update product qty
            $sql = "UPDATE products SET qty = qty - ? WHERE product_id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($qty, $id));

            // cost amount
            $cost_amount = 0;
            $res = $db->prepare("SELECT sum(cost) FROM inventory WHERE invoice_no=:id AND product_id = '$id' ");
            $res->bindParam(':id', $invoice);
            $res->execute();
            for ($f = 0; $ro = $res->fetch(); $f++) {
                $cost_amount = $ro['sum(cost)'];
            }

            $wv_cost = ($cost_amount / 118) * 100;
            // profit
            $profit = $value - $wv_cost;
            $profit = number_format($profit, 2, ".", "");

            // update sales_list
            $sql = "UPDATE sales_list SET profit = ?, cost_amount = ?, cus_id = ?, action = ?, date = ? WHERE id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($profit, $cost_amount, $cus, 0, $date, $list));
        }


        // get sales section
        $result = $db->prepare("SELECT sum(cost_amount),sum(profit),sum(vat),sum(value),sum(amount),sum(dic) FROM sales_list WHERE invoice_no=:id  ");
        $result->bindParam(':id', $invoice);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $cost = $row['sum(cost_amount)'];
            $profit = $row['sum(profit)'];
            $vat = $row['sum(vat)'];
            $value = $row['sum(value)'];
            $amount = $row['sum(amount)'];
            $discount = $row['sum(dic)'];
        }

        $balance = $amount - $payment;

        $root = '';
        $lorry = '';
        $lorry_id = 0;
        $term = 0;
        $load = 0;
        $vat_action = 0;

        $now_date = $now;
        if ($date == $now) {
            $now_date = '';
        }

        // insert sales
        $sql = "INSERT INTO sales (invoice_number,cashier,date,time,amount,balance,discount,cost,profit,name,root,rep,lorry_no,term,loading_id,customer_id,action,address,vat,value,cus_vat_no,vat_action,type,now) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($invoice, $driver, $date, $time, $amount, $balance, $discount, $cost, $profit, $cus_name, $root, $driver_name, $lorry, $term, $load, $cus, 1, $address, $vat, $value, $vat_no, $vat_action, $pay_type, $now_date));

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
        $ql->execute(array($invoice, 'debit', $now, $time, $amount, $cus_balance, $sales_id, $cus, $driver, $driver_name));


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

        // $sales_id = 0;
        $paycose = 'invoice_payment';
        $pay_amount = $payment;
        $credit = $balance;

        // payment section -----------------------------
        if ($pay_type == 'Credit') {

            // insert query
            $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,action,sales_id,customer_id,pay_type,credit_balance,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, 0, $amount, $pay_type, $date, $time, 2, $sales_id, $cus, $pay_type, $credit, $paycose));
        } else if ($pay_type == 'Chq') {

            if ($credit > 0) {

                // insert query
                $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,action,sales_id,customer_id,pay_type,credit_balance,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                $ql = $db->prepare($sql);
                $ql->execute(array($invoice, 0, $credit, 'Credit', $date, $time, 2, $sales_id, $cus, 'Credit', $credit, $paycose));
            }

            // insert query
            $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,chq_no,chq_date,chq_action,action,sales_id,customer_id,pay_type,chq_bank,credit_balance,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, 0, $pay_amount, $pay_type, $date, $time, $chq_no, $chq_date, 0, 2, $sales_id, $cus, $pay_type, $chq_bank, $credit, $paycose));
        } else {

            if ($credit > 0) {

                // insert query
                $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,action,sales_id,customer_id,pay_type,credit_balance,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                $ql = $db->prepare($sql);
                $ql->execute(array($invoice, 0, $credit, 'Credit', $date, $time, 2, $sales_id, $cus, 'Credit', $credit, $paycose));
            }

            // insert query
            $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,action,sales_id,customer_id,pay_type,credit_balance,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, $pay_amount, $pay_amount, $pay_type, $date, $time, 1, $sales_id, $cus, $pay_type, $credit, $paycose));
        }

        if ($pay_type == 'Cash') {
            // Account balancing -----------------
            $user_id = $_SESSION['SESS_MEMBER_ID'];
            $user_name = $_SESSION['SESS_FIRST_NAME'];

            $amount = $payment;

            $cr_id = 1;
            $cr_blc = 0;
            $blc = 0;
            $re = $db->prepare("SELECT * FROM cash WHERE id= :id ");
            $re->bindParam(':id', $cr_id);
            $re->execute();
            for ($k = 0; $r = $re->fetch(); $k++) {
                $blc = $r['amount'];
                $cr_name = $r['name'];
            }

            $cr_blc = $blc + $amount;

            $sql = "UPDATE  cash SET amount=amount+? WHERE id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($amount, $cr_id));

            $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,loading_id,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array('yard_sales', 'Credit', $invoice, 0, $amount, 0, $cr_id, 'sales_cash', $cr_name, $cr_blc, 'sales', 'Yard sales', 0, 0, $now, $time, $user_id, $user_name));
        }

        if ($pay_type != 'Credit') {

            $user_id = $_SESSION['SESS_MEMBER_ID'];
            $user_name = $_SESSION['SESS_FIRST_NAME'];

            $amount = $payment;

            // get sales
            $sales_id = 0;
            $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
            $result->bindParam(':id', $invoice);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $sales_id = $row['transaction_id'];
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
            $sql = "INSERT INTO customer_record (invoice_no,type,date,time,credit,balance,sales_id,customer_id,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($invoice, 'credit', $now, $time, $amount, $cus_balance, $sales_id, $cus, $user_id, $user_name));
        }
    }
    $invoice = base64_encode($invoice);
    header("location: sales_print.php?id=$invoice");
} else {
    $err = 3;

    header("location: sales.php?id=$invoice&err=$err");
}
