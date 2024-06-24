<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");



$id = $_POST['id'];
$column = $_POST['column'];
$value = $_POST['value'];
$load = $_POST['load'];

$sql = "UPDATE payment  SET $column=? WHERE transaction_id=?";
$q = $db->prepare($sql);
$q->execute(array($value, $id));

header("location: unloading_stock.php?id=$load");