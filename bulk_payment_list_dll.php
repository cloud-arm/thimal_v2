<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");



$id = $_GET['id'];
$pay_id = $_GET['pay_id'];


$sql = "UPDATE credit_payment SET action = ?, dll = ? WHERE id = ? ";
$q = $db->prepare($sql);
$q->execute(array(5, 1, $id));

header("location: bulk_payment.php?id=$pay_id");
