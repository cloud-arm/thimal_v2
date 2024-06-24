<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$type = $_POST['type'];
$sup = $_POST['supply'];
$load = $_POST['load'];
$sup_invo = $_POST['sup_invoice'];
$note = $_POST['note'];
$invo = $_POST['invo'];

$pay_type = 'Credit_Note';
$pay_amount = 0;


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


$result = $db->prepare("SELECT sum(amount) FROM purchases_item WHERE invoice=:id ");
$result->bindParam(':id', $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $amount = $row['sum(amount)'];
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
    $sql = "INSERT INTO purchases (invoice_number,amount,remarks,date,supplier_id,supplier_name,supplier_invoice,pay_type,pay_amount,type,user_id,lorry_id,lorry_no,loading_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $re = $db->prepare($sql);
    $re->execute(array($invo, $amount, $note, $date, $sup, $sup_name, $sup_invo, $pay_type, $pay_amount, $type, $userid, $lorry, $lorry_no, $load));


    $sql = "UPDATE  purchases_item SET action=? WHERE invoice=?";
    $ql = $db->prepare($sql);
    $ql->execute(array('close', $invo));

    // payment section
    $sql = 'INSERT INTO supply_payment (amount,pay_amount,pay_type,date,invoice_date,invoice_no,supply_id,supply_name,supplier_invoice,type,credit_balance,reason) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
    $q = $db->prepare($sql);
    $q->execute(array($amount, $pay_amount, $pay_type, $date, $date, $invo, $sup, $sup_name, $sup_invo, $type, $amount, $note));

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

        $qty_blc = $st_qty - $qty;

        $sql = "UPDATE  products SET qty = qty - ?, cost = ? WHERE product_id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($qty, $cost, $p_id));

        $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,cost,sell) VALUES (?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($p_id, $name, $invo, 'out', $qty_blc, $qty, $date, $cost, $sell));

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

            $qty_blc = $st_qty - $qty;

            if ($sell == $st_sell & $cost == $st_cost & $sup == $st_sup & $p_id == $st_p) {

                $sql = "UPDATE stock SET qty=qty-?, qty_balance=qty_balance-?, invoice_no=? WHERE id=?";
                $ql = $db->prepare($sql);
                $ql->execute(array($qty, $qty, $invo, $st_id));
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



    $y = date("Y");
    $m = date("m");

    header("location: grn_return_rp.php?year=$y&month=$m");
} else {

    header("location: grn_return.php?id=$invo&err=1");
}
