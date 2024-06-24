<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$ui = $_SESSION['SESS_MEMBER_ID'];
$un = $_SESSION['SESS_FIRST_NAME'];

$load = $_POST['id'];
$type = $_POST['type'];
$sup = $_POST['supply'];
$sup_invo = $_POST['sup_invoice'];
$note = $_POST['note'];
$pay_amount = $_POST['amount'];

$pay_type = $_POST['pay_type'];
$transport = $_POST['transport'];
$loc_id = $_POST['location'];
$pu_date = $_POST['pu_date'];
$invo = $_POST['invo'];
$new_invo = $_POST['new_invo'];

$acc_no = '';
$bank = 0;
$bank_name = '';
$acc = 0;
$chq_no = '';
$chq_bank = '';
$chq_date = '';
$chq_amount = 0;

if ($pay_type == 'Bank') {
    $acc_no = $_POST['acc_no'];
    $bank_name = $_POST['bank_name'];
}

if ($pay_type == 'Chq') {
    $bank = $_POST['acc'];
    $chq_no = $_POST['chq_no'];
    $chq_date = $_POST['chq_date'];
    $chq_amount = $_POST['amount'];

    $re = $db->prepare("SELECT * FROM bank_balance WHERE id = :id");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $chq_bank = $r['name'];
    }
}


$result = $db->prepare("SELECT * FROM supplier WHERE supplier_id=:id ");
$result->bindParam(':id', $sup);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sup_name = $row['supplier_name'];
}

$lorry_no = '';
$lorry = 0;

$result = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id ");
$result->bindParam(':id', $load);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $lorry_no = $row['lorry_no'];
    $lorry = $row['lorry_id'];
}


$result = $db->prepare("SELECT sum(amount),sum(discount) FROM purchases_item WHERE invoice=:id ");
$result->bindParam(':id', $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $amount = $row['sum(amount)'];
    $dic = $row['sum(discount)'];
}

$result = $db->prepare("SELECT * FROM purchases_location WHERE id = :id ");
$result->bindParam(':id', $loc_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $location = $row['location'];
}

$date = date("Y-m-d");
$time = date('H:i:s');

$con = 0;
$result = $db->prepare("SELECT * FROM purchases WHERE loading_id=:id  AND invoice_number = '$invo' ");
$result->bindParam(':id', $load);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $con = $row['transaction_id'];
}

if ($con == 0) {

    // purchasing section
    $sql = "INSERT INTO purchases (invoice_number,amount,remarks,date,supplier_id,supplier_name,supplier_invoice,pay_type,pay_amount,discount,type,user_id,transport,lorry_id,lorry_no,chq_no,chq_date,chq_amount,loading_id,location_id,location,pu_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $re = $db->prepare($sql);
    $re->execute(array($invo, $amount, $note, $date, $sup, $sup_name, $sup_invo, $pay_type, $pay_amount, $dic, $type, $ui, $transport, $lorry, $lorry_no, $chq_no, $chq_date, $chq_amount, $load, $loc_id, $location, $pu_date));

    //---Transport details ------
    if ($transport > 0) {

        $sql = "UPDATE  purchases_location SET transport=? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($transport, $loc_id));

        $sql = "INSERT INTO transport_record (invoice_no,amount,date,supplier_id,supplier_name,type,user_id,lorry_id,lorry_no,time) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $re = $db->prepare($sql);
        $re->execute(array($invo, $transport, $date, $sup, $sup_name, 0, $ui, $lorry, $lorry_no, $time));
    }
    //-------------------

    $sql = "UPDATE  purchases_item SET action=?, pu_date=? WHERE invoice=?";
    $ql = $db->prepare($sql);
    $ql->execute(array('active', $pu_date, $invo));

    // payment section
    $credit = 0;
    if ($amount > $pay_amount) {

        $credit = $amount - $pay_amount;

        $sql = 'INSERT INTO supply_payment (amount,pay_amount,pay_type,date,invoice_date,invoice_no,supply_id,supply_name,supplier_invoice,type,credit_balance) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
        $q = $db->prepare($sql);
        $q->execute(array($amount, $pay_amount, 'Credit', $date, $date, $invo, $sup, $sup_name, $sup_invo, $type, $credit));
    }

    if ($pay_amount > 0) {

        $credit_id = 0;
        $re = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no = :id AND pay_type = 'Credit' ");
        $re->bindParam(':id', $invo);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $credit_id = $r['id'];
        }

        if ($pay_type == 'Chq') {
            $sql = 'INSERT INTO supply_payment (amount,pay_amount,pay_type,date,invoice_date,invoice_no,supply_id,supply_name,supplier_invoice,type,chq_no,chq_bank,chq_date,bank_id,action,credit_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $q = $db->prepare($sql);
            $q->execute(array($pay_amount, $pay_amount, $pay_type, $date, $date, $invo, $sup, $sup_name, $sup_invo, $type, $chq_no, $chq_bank, $chq_date, $bank, 1, $credit_id));
        } else {
            $sql = 'INSERT INTO supply_payment (amount,pay_amount,pay_type,date,invoice_date,invoice_no,supply_id,supply_name,supplier_invoice,type,bank_name,acc_no,credit_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $q = $db->prepare($sql);
            $q->execute(array($pay_amount, $pay_amount, $pay_type, $date, $date, $invo, $sup, $sup_name, $sup_invo, $type, $bank_name, $acc_no, $credit_id));
        }
    }

    // stock balance section
    $result = $db->prepare("SELECT * FROM purchases_item WHERE invoice = :id ");
    $result->bindParam(':id', $invo);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $p_id = $row['product_id'];
        $name = $row['name'];
        $qty = $row['qty'];
        $date = $row['date'];
        $sell = $row['sell'];
        $cost = $row['cost'];

        $qty_blc = 0;
        $re = $db->prepare("SELECT * FROM products WHERE product_id = :id ");
        $re->bindParam(':id', $p_id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $st_qty = $r['qty'];
            $code = $r['product_code'];
        }

        $qty_blc = $st_qty + $qty;

        $sql = "UPDATE  products SET qty = ?, cost = ? WHERE product_id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($qty_blc, $cost, $p_id));

        $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,cost,sell) VALUES (?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($p_id, $name, $invo, 'in', $qty_blc, $qty, $date, $cost, $sell));

        $qty_blc = 0;
        $con = 0;
        $re = $db->prepare("SELECT * FROM stock ");
        $re->bindParam(':id', $res);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $st_qty = $r['qty_balance'];
            $st_sell = $r['sell'];
            $st_cost = $r['cost'];
            $st_p = $r['product_id'];
            $st_sup = $r['supply_id'];
            $st_id = $r['id'];

            if ($st_qty == '') {
                $st_qty = 0;
            }

            $qty_blc = $st_qty + $qty;

            if ($sell == $st_sell & $cost == $st_cost & $sup == $st_sup & $p_id == $st_p) {

                $sql = "UPDATE stock SET qty=qty+?, qty_balance=?, invoice_no=? WHERE id=?";
                $ql = $db->prepare($sql);
                $ql->execute(array($qty, $qty_blc, $invo, $st_id));
                $con = 1;
            }
        }

        if ($con == 0) {

            $sql = "INSERT INTO stock (product_id,code,name,invoice_no,qty_balance,qty,date,supply_id,supply_name,sell,cost) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($p_id, $code, $name, $invo, $qty, $qty, $date, $sup, $sup_name, $sell, $cost));
        }

        $stock_id = 0;
        $re = $db->prepare("SELECT * FROM stock WHERE invoice_no = :id ");
        $re->bindParam(':id', $invo);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $stock_id = $r['id'];
            $pid = $r['product_id'];

            $sql = "UPDATE inventory SET stock_id=? WHERE invoice_no=? AND product_id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($stock_id, $invo, $pid));
        }
    }

    // cash acc balance section
    if ($pay_type == 'Cash') {

        $cr_id = 2;

        $cash_blc = 0;
        $blc = 0;
        $re = $db->prepare("SELECT * FROM cash WHERE id = :id ");
        $re->bindParam(':id', $cr_id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $blc = $r['amount'];
            $cr_name = $r['name'];
        }

        $cash_blc = $blc - $pay_amount;

        $cr_type = 'grn_payment';

        $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array('grn', 'Debit', $invo, $pay_amount, 0, 0, $cr_type, 'GRN Payment', 0, $cr_type, $cr_name, $cr_id, $cash_blc, $date, $time, $ui, $un));

        $sql = "UPDATE  cash SET amount=? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($cash_blc, $cr_id));
    }

    if ($new_invo) {
        // unloading section
        $time = date("h:i:sa");

        $result = $db->prepare("SELECT * FROM loading_list WHERE loading_id=:id ");
        $result->bindParam(':id', $load);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $pid = $row['product_code'];
            $name = $row['product_name'];
            $list = $row['transaction_id'];
            $qty = $row['qty'];

            $sql = "UPDATE products SET qty=qty+? WHERE product_id=?";
            $q = $db->prepare($sql);
            $q->execute(array($qty, $pid));

            $sql = "UPDATE loading_list SET unload_qty=unload_qty+? WHERE transaction_id=?";
            $q = $db->prepare($sql);
            $q->execute(array($qty, $list));


            $result1 = $db->prepare("SELECT * FROM products WHERE product_id= :id  ");
            $result1->bindParam(':id', $pid);
            $result1->execute();
            for ($i = 0; $row1 = $result1->fetch(); $i++) {
                $qty_blc = $row1['qty'];
            }

            $sql = "UPDATE loading_list SET yard_before=? WHERE transaction_id=?";
            $q = $db->prepare($sql);
            $q->execute(array($qty_blc, $list));

            $sql = "INSERT INTO stock_log (product_id,qty,product_name,date,time,action,source_id,yard_qty,type,user_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($pid, $qty, $name, $date, $time, 1, $load, $qty_blc, 2, $ui));
        }

        $action = 'unload';

        $sql = "UPDATE loading SET action=?, unloading_time=? WHERE transaction_id =? ";
        $q = $db->prepare($sql);
        $q->execute(array($action, $time, $load));

        $sql = "UPDATE loading_list SET action=? WHERE loading_id=?";
        $q = $db->prepare($sql);
        $q->execute(array($action, $load));

        $sql = "UPDATE lorry SET action=? WHERE loading_id =? ";
        $q = $db->prepare($sql);
        $q->execute(array($action, $load));
    }


    $y = date("Y");
    $m = date("m");

    $dep = $_SESSION['SESS_DEPARTMENT'];

    if (!$new_invo) {
        header("location: grn.php");
    } else if ($dep == 'logistic') {
        header("location: index.php");
    } else {
        header("location: grn_rp.php?year=$y&month=$m");
    }
} else {

    header("location: grn.php?id=$load&invo=$invo&err=1");
}
