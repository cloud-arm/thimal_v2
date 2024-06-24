<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$d1 = $_GET['d1'];
$d2 = $_GET['d2'];


$date = date("Y-m-d");
$time = date('H:i:s');

$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$invo = date('ymdhis');

$total = 0;
$result = $db->prepare("SELECT *,purchases_item.transaction_id AS tr_id, purchases_item.qty AS qt FROM purchases_item JOIN purchases ON purchases_item.invoice = purchases.invoice_number JOIN products ON purchases_item.product_id = products.product_id WHERE purchases_item.action = 'active' AND (products.transport1>0 OR products.transport2>0) AND purchases.pu_date BETWEEN '$d1' AND '$d2' ORDER BY purchases_item.loading_id ");
$result->bindParam(':userid', $date);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $trans = 0;
    if ($row['location_id'] == 1) {
        $trans = $row['transport1'];
    }
    if ($row['location_id'] == 2) {
        $trans = $row['transport2'];
    }

    $amount = $row['qt'] * $trans;

    $sql = "INSERT INTO transport_list (invoice_no,product_id,product_name,qty,amount,transport,sup_invoice,pu_invoice,pu_date,location,location_id,lorry_id,lorry_no,loading_id,date,time,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($invo, $row['product_id'], $row['gen_name'], $row['qt'], $amount, $trans, $row['supplier_invoice'], $row['invoice_number'], $row['pu_date'], $row['location'], $row['location_id'], $row['lorry_id'], $row['lorry_no'], $row['loading_id'], $date, $time, $userid));

    $total += $amount;
}

$sup_id = 1;
$result = $db->prepare("SELECT * FROM supplier WHERE supplier_id = :id ");
$result->bindParam(':id', $sup_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sup_name = $row['supplier_name'];
}

$sql = "INSERT INTO transport (invoice_number,date_from,date_to,remarks,amount,pay_amount,supplier_id,supplier_name,date,time,userid,username) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
$q = $db->prepare($sql);
$q->execute(array($invo, $d1, $d2, '', $total, 0, $sup_id, $sup_name, $date, $time, $userid, $username));

$invo = base64_encode($invo);

header("location: transport_rp_print.php?invo=$invo");
