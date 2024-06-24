<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('connect.php');

$user = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$unit = $_POST['unit'];

if ($unit == 1) {

    $complain_no = $_POST['complain_no'];
    $customer = $_POST['customer'];
    $cylinder_no = $_POST['cylinder_no'];
    $product = $_POST['product'];
    $reason = $_POST['reason'];
    $gas_weight = $_POST['gas_weight'];
    $comment = $_POST['comment'];
    $qty = $_POST['qty'];
    $load = $_POST['load'];

    $date = date("Y-m-d");
    $invoice = date('ymdhis');

    $repl = 0;
    $one2one = false;
    if (isset($_POST['one2one'])) {
        $repl = 1;
        $one2one = true;
    }


    $type = 'damage';
    $action = "register";


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


    $lorry = 0;
    $lorry_no = '';
    $result = $db->prepare("SELECT * FROM loading WHERE transaction_id =:id ");
    $result->bindParam(':id', $load);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $lorry = $row['lorry_id'];
        $lorry_no = $row['lorry_no'];
    }

    $result = $db->prepare("SELECT * FROM damage_reason WHERE id = :id ");
    $result->bindParam(':id', $reason);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $reason_name = $row['name'];
    }


    if ($one2one) {

        $re = $db->prepare("SELECT * FROM stock WHERE product_id=:id AND qty_balance>0  ORDER BY id ASC LIMIT 1 ");
        $re->bindParam(':id', $product);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $st_qty = $r['qty_balance'];
            $st_id = $r['id'];
            $sell = $r['sell'];
            $cost = $r['cost'];
        }

        $sql = "UPDATE stock  SET qty_balance = qty_balance - ? WHERE id = ? ";
        $q = $db->prepare($sql);
        $q->execute(array($qty, $st_id));

        $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,sell,cost,stock_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($product, $product_name, $invoice, 'out', $st_qty - $qty, $qty, $date, $sell, $cost, $st_id));
    }


    $sql = "UPDATE products  SET damage = damage + ? WHERE product_id = ?";
    $q = $db->prepare($sql);
    $q->execute(array($qty, $product));


    $sql = "INSERT INTO damage (complain_no,customer_id,customer_name,product_id,cylinder_no,cylinder_type,reason_id,reason,date,action,gas_weight,comment,type,location,invoice_no,position,replacement,loading_id,qty,lorry_id,lorry_no,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($complain_no, $customer, $customer_name, $product, $cylinder_no, $product_name, $reason, $reason_name, $date, $action, $gas_weight, $comment, $type, 'Yard', $invoice, 1, $repl, $load, $qty, $lorry, $lorry_no, $user, $user_name));


    $sql = "INSERT INTO damage_order (complain_no,date,action,type,location,loading_id) VALUES (?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($complain_no, $date, $action, $type, 'Yard', $load));

    header("location: damage_view.php");
}

if ($unit == 2) {
    $type = $_POST['name'];
    $id = 0;

    $name = trim($type);
    $name = ucwords($name);
    $name = str_replace(" ", "_", $name);

    $id = 0;
    $re = $db->prepare("SELECT * FROM damage_reason WHERE name=:id ");
    $re->bindParam(':id', $name);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $id = $r['id'];
    }

    if ($id == 0) {
        $sql = "INSERT INTO damage_reason (name) VALUES (?) ";
        $ql = $db->prepare($sql);
        $ql->execute(array($name));
    }

    header("location: damage.php");
}
