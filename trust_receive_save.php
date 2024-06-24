<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");



$id = $_POST['id'];
$location = $_POST['location'];
$qty = $_POST['qty'];

$sql = "UPDATE trust  SET qty_receive=qty_receive+? WHERE transaction_id=?";
$q = $db->prepare($sql);
$q->execute(array($qty, $id));

$result = $db->prepare("SELECT * FROM trust WHERE  transaction_id= :id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $qty_blc = $row['qty'];
    $qty_receive = $row['qty_receive'];
    $pr = $row['product'];
    $pro_id = $row['product_id'];
}


if ($qty_blc == $qty_receive) {

    $status = 'clear';

    $sql = "UPDATE trust  SET status=? WHERE transaction_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($status, $id));
} else {

    $status = 'processing';

    $sql = "UPDATE trust  SET status=? WHERE transaction_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($status, $id));
}


$sql = "UPDATE products  SET qty=qty+? WHERE product_id=?";
$q = $db->prepare($sql);
$q->execute(array($qty, $pro_id));

$sql = "UPDATE products  SET trust=trust-? WHERE product_id=?";
$q = $db->prepare($sql);
$q->execute(array($qty, $pro_id));


header("location: trust_view.php");
